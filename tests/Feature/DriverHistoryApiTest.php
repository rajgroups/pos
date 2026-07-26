<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DriverHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'http://127.0.0.1:9502/*' => Http::response('success', 200),
        ]);

        Redis::shouldReceive('set')->byDefault()->andReturnTrue();
        Redis::shouldReceive('del')->byDefault()->andReturn(1);
    }

    public function test_driver_can_fetch_history_and_dashboard()
    {
        $driver = Driver::create([
            'name' => 'Test Driver',
            'phone' => '9876543210',
            'license_number' => 'DL-99999',
            'is_online' => true,
        ]);

        $user = User::create([
            'name' => 'Test Passenger',
            'phone' => '9123456789',
        ]);

        $category = VehicleType::create([
            'type_key' => 'cab',
            'name' => 'Cab',
            'slug' => 'cab',
        ]);

        $booking = Booking::create([
            'booking_no' => 'BK-100',
            'user_id' => $user->id,
            'driver_id' => $driver->id,
            'vehicle_category_id' => $category->id,
            'service_mode' => 'instant',
            'status' => 'completed',
            'estimated_amount' => 250.00,
            'final_amount' => 250.00,
        ]);

        Sanctum::actingAs($driver, ['*']);

        $historyResponse = $this->getJson('/api/driver/bookings?page=1&per_page=15&sort_by=Date%3A+Newest');
        $historyResponse->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['data', 'meta' => ['total_rides', 'total_spent', 'average_rating']]);

        $dashboardResponse = $this->getJson('/api/driver/dashboard');
        $dashboardResponse->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['data' => ['is_online', 'today_earnings', 'today_trips', 'recent_trips']]);
    }

    public function test_driver_cannot_go_offline_with_active_ride()
    {
        $driver = Driver::create([
            'name' => 'Active Driver',
            'phone' => '9876543211',
            'license_number' => 'DL-88888',
            'is_online' => true,
        ]);

        $user = User::create([
            'name' => 'Passenger',
            'phone' => '9123456780',
        ]);

        $category = VehicleType::create([
            'type_key' => 'auto',
            'name' => 'Auto',
            'slug' => 'auto',
        ]);

        $activeBooking = Booking::create([
            'booking_no' => 'BK-ACTIVE',
            'user_id' => $user->id,
            'driver_id' => $driver->id,
            'vehicle_category_id' => $category->id,
            'service_mode' => 'instant',
            'status' => 'started',
            'estimated_amount' => 150.00,
        ]);

        Sanctum::actingAs($driver, ['*']);

        // Attempting to go offline should be rejected
        $response = $this->postJson('/api/driver/profile/online-status', [
            'is_online' => false,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'You cannot go offline while you have an active ride.');
    }
}
