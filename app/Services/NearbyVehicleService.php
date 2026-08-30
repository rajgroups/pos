<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleLocation;
use App\Services\Socket\DriverPresenceStore;
use Illuminate\Support\Facades\Redis;

class NearbyVehicleService
{
    public function __construct(protected DriverPresenceStore $presenceStore)
    {
    }

    public function getNearbyVehicles(
        int $vehicleCategoryId,
        float $latitude,
        float $longitude,
        float $radiusKm = 5.0
    ): array {
        // 1. Validate category exists
        $category = VehicleCategory::find($vehicleCategoryId);
        if (!$category) {
            return [];
        }

        // 2. Resolve category and its subcategories
        $categoryIds = [$vehicleCategoryId];
        $subCategoryIds = VehicleCategory::where('parent_id', $vehicleCategoryId)->pluck('id')->toArray();
        $categoryIds = array_merge($categoryIds, $subCategoryIds);

        // 3. Find nearby driver IDs using Redis
        $redisDriverIds = $this->presenceStore->findNearbyDriverIds($latitude, $longitude, $radiusKm);

        // Fetch location details from Redis and check staleness (threshold of 2 minutes / 120 seconds)
        $staleThreshold = 120; // seconds
        $nearbyDrivers = [];

        foreach ($redisDriverIds as $driverId) {
            $locationData = Redis::get("driver:location:{$driverId}");
            if ($locationData) {
                $location = json_decode($locationData, true);
                if ($location && isset($location['updated_at'])) {
                    $updatedAt = \Carbon\Carbon::parse($location['updated_at']);
                    if (now()->diffInSeconds($updatedAt) <= $staleThreshold) {
                        $nearbyDrivers[$driverId] = [
                            'latitude' => (float) $location['latitude'],
                            'longitude' => (float) $location['longitude'],
                            'updated_at' => $updatedAt,
                        ];
                    }
                }
            }
        }

        // 4. Query DB for active and available drivers
        $busyDriverIds = Booking::query()
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->whereNotNull('driver_id')
            ->pluck('driver_id')
            ->toArray();

        $eligibleVehicles = Vehicle::query()
            ->whereIn('vehicle_category_id', $categoryIds)
            ->where('status', 'active')
            ->whereNotNull('driver_id')
            ->whereNotIn('driver_id', $busyDriverIds)
            ->whereHas('driver', function ($query) {
                $query->where('status', 'active')
                    ->where('is_online', 1);
            })
            ->with(['driver', 'category'])
            ->get();

        $vehiclesList = [];

        foreach ($eligibleVehicles as $vehicle) {
            $driverId = $vehicle->driver_id;
            $lat = null;
            $lng = null;
            $locUpdatedAt = null;

            if (isset($nearbyDrivers[$driverId])) {
                $lat = $nearbyDrivers[$driverId]['latitude'];
                $lng = $nearbyDrivers[$driverId]['longitude'];
                $locUpdatedAt = $nearbyDrivers[$driverId]['updated_at'];
            } else {
                // Fallback to database locations table
                $dbLocation = VehicleLocation::where('vehicle_id', $vehicle->id)
                    ->latest('location_updated_at')
                    ->first();

                if ($dbLocation && $dbLocation->location_updated_at) {
                    $locUpdatedAt = \Carbon\Carbon::parse($dbLocation->location_updated_at);
                    if (now()->diffInSeconds($locUpdatedAt) <= $staleThreshold) {
                        $lat = (float) $dbLocation->latitude;
                        $lng = (float) $dbLocation->longitude;
                    }
                }
            }

            if ($lat === null || $lng === null) {
                continue;
            }

            // Calculate distance using PHP's deg2rad
            $distance = $this->calculateDistance($latitude, $longitude, $lat, $lng);

            if ($distance > $radiusKm) {
                continue;
            }

            $vehiclesList[] = [
                'driver_id' => $driverId,
                'vehicle_id' => $vehicle->id,
                'vehicle_category_id' => $vehicle->vehicle_category_id,
                'vehicle_number' => $vehicle->vehicle_number,
                'latitude' => $lat,
                'longitude' => $lng,
                'distance_km' => round($distance, 2),
                'icon_url' => $vehicle->category?->icon
                    ? asset('storage/' . ltrim($vehicle->category->icon, '/'))
                    : null,
                'location_updated_at' => $locUpdatedAt ? $locUpdatedAt->toIso8601String() : null,
            ];
        }

        usort($vehiclesList, fn($a, $b) => $a['distance_km'] <=> $b['distance_km']);

        return [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'icon_url' => $category->icon
                    ? asset('storage/' . ltrim($category->icon, '/'))
                    : null,
            ],
            'search' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'radius_km' => $radiusKm,
            ],
            'vehicles' => $vehiclesList,
        ];
    }

    protected function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
