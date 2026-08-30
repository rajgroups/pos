<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class NearbyVehicleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_nearby_vehicles_api(): void
    {
        // 1. Create a user and authenticate
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        // 2. Create vehicle category
        $category = VehicleCategory::create([
            'name' => 'Car',
            'slug' => 'car',
            'is_active' => true,
        ]);

        // 3. Create a driver
        $driver = Driver::create([
            'name' => 'Test Driver',
            'phone' => '9876543210',
            'email' => 'driver@example.com',
            'city' => 'Chennai',
            'state' => 'Tamil Nadu',
            'driver_type' => 'car',
            'license_categories' => ['LMV'],
            'license_number' => 'DL-12345678901',
            'status' => 'active',
            'is_online' => true,
        ]);

        // 4. Create a vehicle
        $vehicle = Vehicle::create([
            'driver_id' => $driver->id,
            'vehicle_category_id' => $category->id,
            'vehicle_number' => 'TN01AB1234',
            'brand' => 'Maruti',
            'model' => 'Swift',
            'status' => 'active',
            'is_verified' => true,
        ]);

        // 5. Setup driver location in Redis GEO and cache
        $presenceStore = app(\App\Services\Socket\DriverPresenceStore::class);
        $presenceStore->updateDriverLocation($driver->id, 13.0841, 80.2721);

        // Also add database vehicle location as a fallback
        VehicleLocation::create([
            'vehicle_id' => $vehicle->id,
            'latitude' => 13.0841,
            'longitude' => 80.2721,
            'location_updated_at' => now(),
        ]);

        // 6. Call nearby vehicle API
        $response = $this->getJson("/api/user/vehicles/nearby?vehicle_category_id={$category->id}&latitude=13.0827&longitude=80.2707&radius=5");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'category' => ['id', 'name', 'icon_url'],
                    'search' => ['latitude', 'longitude', 'radius_km'],
                    'vehicles' => [
                        '*' => [
                            'driver_id',
                            'vehicle_id',
                            'vehicle_category_id',
                            'vehicle_number',
                            'latitude',
                            'longitude',
                            'distance_km',
                            'icon_url',
                            'location_updated_at',
                        ]
                    ]
                ]
            ]);

        // Clean up Redis GEO after test
        $presenceStore->forgetDriver($driver->id);
    }
}
