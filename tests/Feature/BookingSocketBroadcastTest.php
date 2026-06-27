<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategoryPricing;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingSocketBroadcastTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure we fake Redis calls
        Redis::shouldReceive('publish')->byDefault();
        Redis::shouldReceive('georadius')->byDefault()->andReturn([]);
    }

    public function test_booking_actions_publish_to_user_socket_notifications(): void
    {
        // 1. Create a user
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // 2. Setup pricing categories
        $category = VehicleType::create([
            'type_key' => 'cab',
            'name' => 'Cab',
            'slug' => 'cab',
            'description' => 'Cab category',
            'tagline' => 'Fast city rides',
            'starting_fare' => 'From Rs 40',
            'icon' => 'local_taxi_rounded',
            'accent_color' => '#0F766E',
            'gradient_start' => '#F0FDFA',
            'gradient_end' => '#CCFBF1',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        VehicleCategoryPricing::create([
            'vehicle_category_id' => $category->id,
            'pricing_type' => 'distance',
            'base_fare' => 40,
            'minimum_fare' => 80,
            'per_km_rate' => 12,
            'per_hour_rate' => 0,
            'per_day_rate' => 0,
            'per_acre_rate' => 0,
            'per_ton_rate' => 0,
            'waiting_charge_per_hour' => 0,
            'night_charge_percentage' => 0,
            'surge_multiplier' => 1,
            'is_active' => true,
        ]);

        $driver = Driver::create([
            'name' => 'Test Driver',
            'phone' => '9000000999',
            'email' => 'driver@example.com',
            'city' => 'Chennai',
            'state' => 'Tamil Nadu',
            'driver_type' => 'car',
            'license_categories' => ['LMV'],
            'license_number' => 'DL-99999999999',
            'status' => 'active',
            'is_verified' => true,
        ]);

        $vehicle = Vehicle::create([
            'vehicle_category_id' => $category->id,
            'vehicle_number' => 'TN01AB9999',
            'status' => 'active',
            'is_verified' => true,
        ]);

        // Expect Redis::publish to be called for swoole_user_notifications during booking store
        Redis::shouldReceive('publish')
            ->once()
            ->with('swoole_user_notifications', \Mockery::on(function ($argument) use ($user) {
                $data = json_decode($argument, true);
                return isset($data['user_id']) && 
                    $data['user_id'] === $user->id &&
                    $data['payload']['type'] === 'booking_status' &&
                    $data['payload']['booking']['status'] === 'pending';
            }));

        // Trigger booking creation
        $storeResponse = $this->postJson('/api/user/bookings', [
            'vehicle_category_id' => $category->id,
            'payment_method' => 'cash',
            'locations' => [
                [
                    'location_type' => 'pickup',
                    'latitude' => 13.0827,
                    'longitude' => 80.2707,
                    'address' => 'Chennai Pickup',
                    'sequence' => 1,
                ],
                [
                    'location_type' => 'drop',
                    'latitude' => 13.0674,
                    'longitude' => 80.2376,
                    'address' => 'Chennai Drop',
                    'sequence' => 2,
                ],
            ],
            'usage' => [
                'distance_km' => 10,
            ],
        ]);

        $storeResponse->assertCreated();
        $bookingNo = $storeResponse->json('data.booking_no');
        $booking = Booking::where('booking_no', $bookingNo)->firstOrFail();

        // 3. Act as Driver to accept booking, expect both driver and user notification
        Sanctum::actingAs($driver);

        Redis::shouldReceive('publish')
            ->once()
            ->with('swoole_driver_notifications', \Mockery::on(function ($argument) use ($driver) {
                $data = json_decode($argument, true);
                return isset($data['driver_id']) && 
                    $data['driver_id'] === $driver->id &&
                    $data['payload']['booking']['status'] === 'accepted';
            }));

        Redis::shouldReceive('publish')
            ->once()
            ->with('swoole_user_notifications', \Mockery::on(function ($argument) use ($user) {
                $data = json_decode($argument, true);
                return isset($data['user_id']) && 
                    $data['user_id'] === $user->id &&
                    $data['payload']['booking']['status'] === 'accepted';
            }));

        $this->postJson("/api/driver/bookings/{$bookingNo}/accept", [
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
        ])->assertOk();

        // 4. Act as Driver to start booking, expect both driver and user notification
        $booking->refresh();

        Redis::shouldReceive('publish')
            ->once()
            ->with('swoole_driver_notifications', \Mockery::on(function ($argument) use ($driver) {
                $data = json_decode($argument, true);
                return isset($data['driver_id']) && 
                    $data['driver_id'] === $driver->id &&
                    $data['payload']['booking']['status'] === 'started';
            }));

        Redis::shouldReceive('publish')
            ->once()
            ->with('swoole_user_notifications', \Mockery::on(function ($argument) use ($user) {
                $data = json_decode($argument, true);
                return isset($data['user_id']) && 
                    $data['user_id'] === $user->id &&
                    $data['payload']['booking']['status'] === 'started';
            }));

        $this->postJson("/api/driver/bookings/{$bookingNo}/start", [
            'start_otp' => $booking->start_otp,
        ])->assertOk();
    }
}
