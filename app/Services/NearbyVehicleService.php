<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleLocation;
use App\Services\Socket\DriverPresenceStore;
use Illuminate\Support\Facades\Redis;

/**
 * NearbyVehicleService
 *
 * Finds drivers eligible for a booking based on:
 *   1. Vehicle category match
 *   2. Driver/vehicle active status
 *   3. Driver online status
 *   4. No active booking conflict
 *   5. Category-specific search radius (vehicle_categories.driver_search_radius_km)
 *   6. Driver location freshness (stale threshold)
 *
 * Bug fix (2026-09-24):
 *   - Previously used a hard-coded default radius of 5 km, ignoring category config.
 *   - Now reads driver_search_radius_km from vehicle_categories for every search.
 *   - Both Prime Mode (Redis GEO) and Economy Mode (MySQL) enforce the same radius.
 *   - The radius passed to findNearbyDriverIds() was also not propagated from the
 *     category; this is now fixed end-to-end.
 */
class NearbyVehicleService
{
    /** Maximum age of a driver location before it is considered stale (seconds). */
    protected int $staleThresholdSeconds = 120;

    public function __construct(protected DriverPresenceStore $presenceStore)
    {
    }

    /**
     * Find vehicles/drivers near a pickup location.
     *
     * The radius is read from vehicle_categories.driver_search_radius_km.
     * If the column is not yet present (pre-migration), the default 5 km applies.
     *
     * @param  int    $vehicleCategoryId
     * @param  float  $latitude          Pickup latitude
     * @param  float  $longitude         Pickup longitude
     * @return array
     */
    public function getNearbyVehicles(
        int $vehicleCategoryId,
        float $latitude,
        float $longitude
    ): array {
        // 1. Validate & load category
        $category = VehicleCategory::find($vehicleCategoryId);
        if (! $category) {
            return [];
        }

        // 2. Read category-specific radius
        //    driver_search_radius_km is added by migration 2026_09_24_100001.
        //    Fallback to 5 km for backward compat if column is missing.
        $radiusKm = isset($category->driver_search_radius_km)
            ? max(0.1, (float) $category->driver_search_radius_km)
            : 5.0;

        // 3. Resolve category tree (category + its subcategories)
        $categoryIds = collect([$vehicleCategoryId]);
        $subIds = VehicleCategory::where('parent_id', $vehicleCategoryId)
            ->pluck('id');
        $categoryIds = $categoryIds->merge($subIds)->unique()->values()->toArray();

        // 4. Prime Mode: get candidate driver IDs from Redis GEO within radius
        $redisDriverIds = [];
        $nearbyDrivers  = [];

        $isEconomy = app(IndicabModeService::class)->isEconomy();

        if (! $isEconomy) {
            // Pass the category radius to Redis GEO — fixes the radius bug in Prime Mode
            $redisDriverIds = $this->presenceStore->findNearbyDriverIds(
                $latitude,
                $longitude,
                $radiusKm // <-- category-specific radius, NOT hard-coded 5
            );

            foreach ($redisDriverIds as $driverId) {
                $locationData = Redis::get("driver:location:{$driverId}");
                if ($locationData) {
                    $location = json_decode($locationData, true);
                    if ($location && isset($location['updated_at'])) {
                        $updatedAt = \Carbon\Carbon::parse($location['updated_at']);
                        if (now()->diffInSeconds($updatedAt) <= $this->staleThresholdSeconds) {
                            $nearbyDrivers[$driverId] = [
                                'latitude'   => (float) $location['latitude'],
                                'longitude'  => (float) $location['longitude'],
                                'updated_at' => $updatedAt,
                            ];
                        }
                    }
                }
            }
        }

        // 5. Exclude drivers with active bookings
        $busyDriverIds = Booking::query()
            ->whereIn('status', Booking::ACTIVE_STATUSES)
            ->whereNotNull('driver_id')
            ->pluck('driver_id')
            ->toArray();

        // 6. Query eligible vehicles (category match + active + driver online)
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
            $driverId    = $vehicle->driver_id;
            $lat         = null;
            $lng         = null;
            $locUpdatedAt = null;

            // 7. Resolve driver location
            if (isset($nearbyDrivers[$driverId])) {
                // Prime Mode: location already fetched from Redis
                $lat         = $nearbyDrivers[$driverId]['latitude'];
                $lng         = $nearbyDrivers[$driverId]['longitude'];
                $locUpdatedAt = $nearbyDrivers[$driverId]['updated_at'];
            } else {
                // Economy Mode (or driver not in Redis GEO): fall back to MySQL vehicle_locations
                $dbLocation = VehicleLocation::where('vehicle_id', $vehicle->id)
                    ->latest('location_updated_at')
                    ->first();

                if ($dbLocation && $dbLocation->location_updated_at) {
                    $locUpdatedAt = \Carbon\Carbon::parse($dbLocation->location_updated_at);
                    if (now()->diffInSeconds($locUpdatedAt) <= $this->staleThresholdSeconds) {
                        $lat = (float) $dbLocation->latitude;
                        $lng = (float) $dbLocation->longitude;
                    }
                }
            }

            if ($lat === null || $lng === null) {
                continue; // No valid recent location
            }

            // 8. Calculate PICKUP distance (driver → pickup, NOT driver → destination)
            $distance = $this->haversineDistanceKm($latitude, $longitude, $lat, $lng);

            // 9. ENFORCE category radius — reject drivers outside configured max
            //    This is the primary fix for the "5 km search returning 10 km driver" bug.
            if ($distance > $radiusKm) {
                continue;
            }

            $vehiclesList[] = [
                'driver_id'          => $driverId,
                'vehicle_id'         => $vehicle->id,
                'vehicle_category_id'=> $vehicle->vehicle_category_id,
                'vehicle_number'     => $vehicle->vehicle_number,
                'latitude'           => $lat,
                'longitude'          => $lng,
                'distance_km'        => round($distance, 2),
                'icon_url'           => $vehicle->category?->icon
                    ? asset('storage/' . ltrim($vehicle->category->icon, '/'))
                    : null,
                'location_updated_at'=> $locUpdatedAt?->toIso8601String(),
            ];
        }

        // 10. Sort by nearest pickup distance ascending
        usort($vehiclesList, fn ($a, $b) => $a['distance_km'] <=> $b['distance_km']);

        return [
            'category' => [
                'id'              => $category->id,
                'name'            => $category->name,
                'icon_url'        => $category->icon
                    ? asset('storage/' . ltrim($category->icon, '/'))
                    : null,
                'search_radius_km'=> $radiusKm,
            ],
            'search'  => [
                'latitude'  => $latitude,
                'longitude' => $longitude,
                'radius_km' => $radiusKm,
            ],
            'vehicles' => $vehiclesList,
        ];
    }

    /**
     * Haversine formula: great-circle distance between two lat/lng points in km.
     *
     * Returns the distance between the DRIVER's current location and the
     * PICKUP location — NOT the destination. This matches business rule #4.
     */
    public function haversineDistanceKm(
        float $pickupLat,
        float $pickupLng,
        float $driverLat,
        float $driverLng
    ): float {
        $earthRadius = 6371.0;
        $dLat = deg2rad($driverLat - $pickupLat);
        $dLng = deg2rad($driverLng - $pickupLng);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($pickupLat)) * cos(deg2rad($driverLat))
            * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
