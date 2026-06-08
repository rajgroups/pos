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

        // Find nearby drivers before updating current driver's location
        $nearbyDrivers = $this->findNearbyDrivers(
            $longitude,
            $latitude,
            $payload['radius'] ?? self::NEARBY_RADIUS_KM
        );

        // Store driver location with GeoHash
        $this->storeDriverLocation($driverId, $latitude, $longitude, $geoHash);

        // Also store for quick lookup by GeoHash prefix
        $this->storeDriverGeoHashIndex($driverId, $geoHash);

        // Update driver status timestamp
        $this->updateDriverHeartbeat($driverId);

        // Broadcast location to all connected clients
        $this->broadcastLocation($server, $payload);

        // If there are nearby drivers, notify them
        if (!empty($nearbyDrivers)) {
            $this->notifyNearbyDrivers($server, $driverId, $nearbyDrivers);
        }
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
        // Convert km to meters for Redis GEORADIUS
        $radiusMeters = $radiusKm * 1000;

        $nearbyDriverIds = Redis::georadius(
            'drivers:online',
            $longitude,
            $latitude,
            $radiusMeters,
            'km',
            ['WITHDIST', 'ASC']
        );

        $nearbyDrivers = [];
        foreach ($nearbyDriverIds as $driverData) {
            // Skip if not an array (sometimes Redis returns simple array)
            if (is_array($driverData)) {
                $nearbyDrivers[] = [
                    'driver_id' => $driverData[0],
                    'distance_km' => round($driverData[1], 2)
                ];
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
            $server->push($fd, $message);
        }
    }

    /**
     * Notify nearby drivers about a new driver in their vicinity
     */
    private function notifyNearbyDrivers($server, string $newDriverId, array $nearbyDrivers): void
    {
        foreach ($nearbyDrivers as $driver) {
            $message = json_encode([
                'type' => 'nearby_driver',
                'driver_id' => $newDriverId,
                'distance_km' => $driver['distance_km'],
                'message' => "Driver {$newDriverId} is nearby ({$driver['distance_km']}km away)"
            ]);

            $server->push($driver['driver_id'], $message);
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
        Redis::zrem('drivers:online', $driverId);
        Redis::del("driver:location:{$driverId}");
        Redis::del("driver:status:{$driverId}");
    }
}
