<?php

namespace App\Services\Socket;

use Illuminate\Support\Facades\Redis;

class DriverSearchService
{
    /**
     * Find drivers near a location
     */
    public function findNearbyDrivers(
        float $longitude,
        float $latitude,
        float $radiusKm = 5,
        array $excludeDrivers = []
    ): array {
        $drivers = Redis::georadius(
            'drivers:online',
            $longitude,
            $latitude,
            $radiusKm,
            'km',
            ['WITHDIST', 'WITHCOORD', 'ASC']
        );

        $result = [];
        if (empty($drivers)) {
            return $result;
        }
        // dd('i');
        // dd($result);
        foreach ($drivers as $key => $driver) {
            $driverId = null;
            $distance = 0;
            $coords = [$longitude, $latitude];

            if (is_object($driver)) {
                $driverId = $driver->member ?? $driver->name ?? $key;
                $distance = $driver->distance ?? 0;
                $coords = $driver->coordinates ?? $coords;
            } elseif (is_array($driver)) {
                if (count($driver) >= 3) {
                    $driverId = $driver[0];
                    $distance = $driver[1];
                    $coords = $driver[2];
                } elseif (isset($driver['distance'])) {
                    $driverId = $key;
                    $distance = $driver['distance'];
                    $coords = $driver['coordinates'] ?? $coords;
                } else {
                    $driverId = $driver[0] ?? $key;
                    $distance = $driver[1] ?? 0;
                }
            } elseif (is_string($driver)) {
                $driverId = $driver;
            } else {
                $driverId = $key;
            }

            // Skip excluded drivers
            if (!$driverId || in_array($driverId, $excludeDrivers)) {
                continue;
            }

            $result[] = [
                'driver_id' => $driverId,
                'distance_km' => round((float) $distance, 2),
                'coordinates' => [
                    'longitude' => $coords[0] ?? $longitude,
                    'latitude' => $coords[1] ?? $latitude
                ]
            ];
        }
        // dd($result);
        return $result;
    }

    /**
     * Find drivers by GeoHash prefix (faster for large datasets)
     */
    public function findDriversByGeoHashPrefix(string $geoHashPrefix): array
    {
        return Redis::smembers("geohash:{$geoHashPrefix}") ?? [];
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    public function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * asin(sqrt($a));

        return $earthRadius * $c;
    }

    /**
     * Get driver's current location
     */
    public function getDriverLocation(string $driverId): ?array
    {
        $location = Redis::hgetall("driver:location:{$driverId}");

        if (empty($location)) {
            // Fallback to GEO position if detailed location not found
            $geoPosition = Redis::geopos('drivers:online', $driverId);
            if ($geoPosition && isset($geoPosition[0][0])) {
                return [
                    'longitude' => $geoPosition[0][0],
                    'latitude' => $geoPosition[0][1],
                    'status' => 'active'
                ];
            }
            return null;
        }

        return [
            'latitude' => (float)$location['latitude'],
            'longitude' => (float)$location['longitude'],
            'geo_hash' => $location['geo_hash'],
            'updated_at' => $location['updated_at'],
            'status' => $location['status']
        ];
    }

    /**
     * Check if driver is online and active
     */
    public function isDriverOnline(string $driverId): bool
    {
        $heartbeat = Redis::get("driver:heartbeat:{$driverId}");
        $geoExists = Redis::geopos('drivers:online', $driverId);

        return $heartbeat !== null && isset($geoExists[0][0]);
    }

    /**
     * Get count of online drivers
     */
    public function getOnlineDriversCount(): int
    {
        return Redis::zcard('drivers:online');
    }

    /**
     * Remove offline driver
     */
    public function removeDriver(string $driverId): void
    {
        Redis::zrem('drivers:online', $driverId);
        Redis::del("driver:location:{$driverId}");
        Redis::del("driver:heartbeat:{$driverId}");

        // Clean up GeoHash indexes (optional - could be left to expire)
        $geohashes = Redis::keys("geohash:*");
        foreach ($geohashes as $geohash) {
            Redis::srem($geohash, $driverId);
        }
    }
}
