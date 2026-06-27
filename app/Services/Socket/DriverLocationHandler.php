<?php

namespace App\Services\Socket;

use Illuminate\Support\Facades\Redis;

class DriverLocationHandler
{
    private const NEARBY_RADIUS_KM = 5;
    private const NEARBY_RADIUS_M = self::NEARBY_RADIUS_KM * 1000;

    public function handle(
        $server,
        $frame,
        array $payload
    ): void {
        $driverId = $payload['driver_id'];
        $latitude = $payload['latitude'];
        $longitude = $payload['longitude'];

        // Generate GeoHash for the location (precision 7 = ~76m)
        $geoHash = $this->generateGeoHash($latitude, $longitude);

        // Store driver location with GeoHash
        $this->storeDriverLocation($driverId, $latitude, $longitude, $geoHash);

        // Also store for quick lookup by GeoHash prefix
        $this->storeDriverGeoHashIndex($driverId, $geoHash);

        // Check for and send any pending booking requests
        $this->checkForPendingBookings($server, $frame->fd, $driverId);

        // Broadcast location to all connected clients
        $this->broadcastLocation($server, $payload);
    }

    /**
     * Generate GeoHash for given coordinates
     */
    private function generateGeoHash(float $latitude, float $longitude, int $precision = 12): string
    {
        $chars = '0123456789bcdefghjkmnpqrstuvwxyz';
        $bits = [16, 8, 4, 2, 1];
        $latInterval = [-90.0, 90.0];
        $lonInterval = [-180.0, 180.0];
        $geohash = '';
        $isEven = true;
        $bit = 0;
        $ch = 0;

        while (strlen($geohash) < $precision) {
            if ($isEven) {
                $mid = ($lonInterval[0] + $lonInterval[1]) / 2;
                if ($longitude > $mid) {
                    $ch |= $bits[$bit];
                    $lonInterval[0] = $mid;
                } else {
                    $lonInterval[1] = $mid;
                }
            } else {
                $mid = ($latInterval[0] + $latInterval[1]) / 2;
                if ($latitude > $mid) {
                    $ch |= $bits[$bit];
                    $latInterval[0] = $mid;
                } else {
                    $latInterval[1] = $mid;
                }
            }

            $isEven = !$isEven;

            if ($bit < 4) {
                $bit++;
            } else {
                $geohash .= $chars[$ch];
                $bit = 0;
                $ch = 0;
            }
        }

        return $geohash;
    }

    /**
     * Find nearby drivers using Redis GEORADIUS
     */
    private function findNearbyDrivers(float $longitude, float $latitude, float $radiusKm = 5): array
    {
        $nearbyDriverIds = Redis::georadius(
            'drivers:online',
            $longitude,
            $latitude,
            $radiusKm,
            'km',
            ['WITHDIST', 'ASC']
        );

        $nearbyDrivers = [];
        if (empty($nearbyDriverIds)) {
            return $nearbyDrivers;
        }

        foreach ($nearbyDriverIds as $key => $driverData) {
            if (is_object($driverData)) {
                $nearbyDrivers[] = [
                    'driver_id' => $driverData->member ?? $driverData->name ?? $key,
                    'distance_km' => round((float) ($driverData->distance ?? 0), 2)
                ];
            } elseif (is_array($driverData)) {
                if (count($driverData) >= 2 && isset($driverData[1])) {
                    $nearbyDrivers[] = [
                        'driver_id' => $driverData[0],
                        'distance_km' => round((float) $driverData[1], 2)
                    ];
                } elseif (isset($driverData['distance'])) {
                    $nearbyDrivers[] = [
                        'driver_id' => $key,
                        'distance_km' => round((float) $driverData['distance'], 2)
                    ];
                } else {
                    $nearbyDrivers[] = [
                        'driver_id' => $driverData[0] ?? $key,
                        'distance_km' => 0
                    ];
                }
            } elseif (is_string($driverData)) {
                $nearbyDrivers[] = [
                    'driver_id' => $driverData,
                    'distance_km' => 0
                ];
            }
        }

        return $nearbyDrivers;
    }

    /**
     * Store driver location using GeoHash and Redis GEO
     */
    private function storeDriverLocation(string $driverId, float $latitude, float $longitude, string $geoHash): void
    {
        echo "Driver Id: {$driverId}, GeoHash: {$geoHash}\n";
        // Store in Redis GEO for proximity queries
        Redis::geoadd('drivers:online', $longitude, $latitude, $driverId);

        // Store detailed location info with GeoHash
        Redis::hmset("driver:location:{$driverId}", [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'geo_hash' => $geoHash,
            'updated_at' => time(),
            'status' => 'active'
        ]);

        // Set expiration for automatic cleanup (e.g., 60 seconds)
        Redis::expire("driver:location:{$driverId}", 60);
    }

    /**
     * Store driver in GeoHash prefix index for faster lookups
     */
    private function storeDriverGeoHashIndex(string $driverId, string $geoHash): void
    {
        // Store for different prefix lengths (for hierarchical search)
        $prefixLengths = [4, 5, 6, 7, 8];

        foreach ($prefixLengths as $length) {
            $prefix = substr($geoHash, 0, $length);
            Redis::sadd("geohash:{$prefix}", $driverId);
            Redis::expire("geohash:{$prefix}", 120);
        }
    }

    /**
     * Check for pending bookings in the driver's queue and push them.
     */
    private function checkForPendingBookings($server, int $fd, string $driverId): void
    {
        // Non-blockingly pop a message from the driver's queue
        $bookingRequest = Redis::lpop("driver_queue:{$driverId}");

        if ($bookingRequest) {
            echo "Found booking for driver {$driverId}. Pushing to FD {$fd}.\n";
            // If a request exists, push it to the driver
            $server->push($fd, $bookingRequest);
        }
    }

    /**
     * Update driver heartbeat timestamp
     */
    private function updateDriverHeartbeat(string $driverId): void
    {
        Redis::setex("driver:heartbeat:{$driverId}", 60, time());
    }

    /**
     * Broadcast driver location to all connected clients
     */
    private function broadcastLocation($server, array $payload): void
    {
        $message = json_encode([
            'type' => 'driver_location',
            'driver_id' => $payload['driver_id'],
            'latitude' => $payload['latitude'],
            'longitude' => $payload['longitude'],
            'timestamp' => time()
        ]);

        foreach ($server->connections as $fd) {
            if ($server->isEstablished($fd)) {
                $server->push($fd, $message);
            }
        }
    }

    /**
     * Notify nearby drivers about a new driver in their vicinity
     */
    private function notifyNearbyDrivers($server, string $newDriverId, array $nearbyDrivers): void
    {
        foreach ($nearbyDrivers as $driver) {
            // Lookup the driver's actual socket File Descriptor from Redis
            $fd = Redis::get("driver:fd:{$driver['driver_id']}");

            if ($fd && $server->isEstablished((int) $fd)) {
                $message = json_encode([
                    'type' => 'nearby_driver',
                    'driver_id' => $newDriverId,
                    'distance_km' => $driver['distance_km'],
                    'message' => "Driver {$newDriverId} is nearby ({$driver['distance_km']}km away)"
                ]);

                $server->push((int) $fd, $message);
            }
        }
    }

    public function initializeDriver(int $driverId): void
    {
        // Mark driver as online in Redis
        Redis::hset("driver:status:{$driverId}", 'online', true);
        Redis::expire("driver:status:{$driverId}", 60);
    }

    public function markDriverOffline(int $driverId): void
    {
        // Remove from online drivers
        Redis::zrem('drivers:online', (string)$driverId);
        Redis::del("driver:location:{$driverId}");
        Redis::del("driver:status:{$driverId}");
    }
}
