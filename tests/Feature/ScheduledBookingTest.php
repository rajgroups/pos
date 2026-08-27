<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategoryPricing;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScheduledBookingTest extends TestCase
{
    use RefreshDatabase;

    protected string $socketUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->socketUrl = rtrim(config('services.socket.url', 'http://127.0.0.1:9502'), '/');
        Http::fake([
            $this->socketUrl . '/*' => Http::response('success', 200),
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/123'], 200),
            'https://oauth2.googleapis.com/*' => Http::response(['access_token' => 'mock_token'], 200),
        ]);

        Redis::shouldReceive('set')->byDefault()->andReturnTrue();
        Redis::shouldReceive('del')->byDefault()->andReturn(1);
    }

    protected function createCategoryWithPricing(): VehicleType
    {
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
            'base_fare' => 50.00,
            'per_km_rate' => 12.00,
            'per_hour_rate' => 0.00,
            'per_day_rate' => 0.00,
            'per_acre_rate' => 0.00,
            'per_ton_rate' => 0.00,
            'minimum_fare' => 50.00,
            'extra_charge' => 0.00,
            'discount' => 0.00,
        ]);

        return $category;
    }

    public function test_can_create_scheduled_booking(): void
    {
        $category = $this->createCategoryWithPricing();
        $user = User::factory()->create(['device_token' => 'user_device_token_123']);
        Sanctum::actingAs($user);

        $futureTime = now()->addHours(5)->format('Y-m-d H:i:s');

        $response = $this->postJson('/api/user/bookings', [
            'vehicle_category_id' => $category->id,
            'booking_mode' => 'scheduled',
            'payment_method' => 'cash',
            'scheduled_at' => $futureTime,
            'locations' => [
                [
                    'location_type' => 'pickup',
                    'latitude' => 13.0827,
                    'longitude' => 80.2707,
                    'address' => 'Chennai Central',
                    'sequence' => 1,
                ],
                [
                    'location_type' => 'drop',
                    'latitude' => 13.0405,
                    'longitude' => 80.2337,
                    'address' => 'T Nagar',
                    'sequence' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.service_mode', 'scheduled')
            ->assertJsonPath('data.status', 'scheduled');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'service_mode' => 'scheduled',
            'status' => Booking::STATUS_SCHEDULED,
            'scheduled_processed_at' => null,
        ]);
    }

    public function test_process_scheduled_bookings_command_claims_due_booking(): void
    {
        $category = $this->createCategoryWithPricing();
        $user = User::factory()->create(['device_token' => 'user_device_token_456']);

        $driver = Driver::create([
            'name' => 'Test Driver Scheduled',
            'phone' => '9888877771',
            'email' => 'driver_sch@example.com',
            'city' => 'Chennai',
            'state' => 'Tamil Nadu',
            'driver_type' => 'car',
            'license_categories' => ['LMV'],
            'license_number' => 'DL-98888777711',
            'status' => 'active',
            'is_verified' => true,
            'is_online' => 1,
            'fcm_token' => 'driver_fcm_token_789',
        ]);

        Vehicle::create([
            'driver_id' => $driver->id,
            'vehicle_category_id' => $category->id,
            'vehicle_number' => 'TN01AB7777',
            'status' => 'active',
            'is_verified' => true,
        ]);

        $pastTime = now()->subMinutes(5);

        $booking = Booking::create([
            'booking_no' => (string) \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'vehicle_category_id' => $category->id,
            'service_mode' => 'scheduled',
            'scheduled_at' => $pastTime,
            'status' => Booking::STATUS_SCHEDULED,
            'estimated_amount' => 100,
            'final_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'start_otp' => '123456',
        ]);

        $booking->locations()->createMany([
            [
                'location_type' => 'pickup',
                'latitude' => 13.0827,
                'longitude' => 80.2707,
                'address' => 'Pickup Loc',
                'sequence' => 1,
            ],
            [
                'location_type' => 'drop',
                'latitude' => 13.0405,
                'longitude' => 80.2337,
                'address' => 'Drop Loc',
                'sequence' => 2,
            ],
        ]);

        $this->artisan('bookings:process-scheduled')
            ->assertExitCode(0);

        $booking->refresh();

        $this->assertEquals(Booking::STATUS_PENDING, $booking->status);
        $this->assertNotNull($booking->scheduled_processed_at);
        $this->assertNotNull($booking->scheduled_notification_sent_at);
        $this->assertNotNull($booking->driver_search_started_at);
    }

    public function test_future_scheduled_booking_is_not_processed(): void
    {
        $category = $this->createCategoryWithPricing();
        $user = User::factory()->create();

        $futureTime = now()->addHours(2);

        $booking = Booking::create([
            'booking_no' => (string) \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'vehicle_category_id' => $category->id,
            'service_mode' => 'scheduled',
            'scheduled_at' => $futureTime,
            'status' => Booking::STATUS_SCHEDULED,
            'estimated_amount' => 100,
            'final_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'start_otp' => '123456',
        ]);

        $this->artisan('bookings:process-scheduled')
            ->assertExitCode(0);

        $booking->refresh();

        $this->assertEquals(Booking::STATUS_SCHEDULED, $booking->status);
        $this->assertNull($booking->scheduled_processed_at);
    }

    public function test_process_scheduled_bookings_command_is_idempotent(): void
    {
        $category = $this->createCategoryWithPricing();
        $user = User::factory()->create(['device_token' => 'user_device_token_abc']);

        $booking = Booking::create([
            'booking_no' => (string) \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'vehicle_category_id' => $category->id,
            'service_mode' => 'scheduled',
            'scheduled_at' => now()->subMinute(),
            'status' => Booking::STATUS_SCHEDULED,
            'estimated_amount' => 100,
            'final_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'start_otp' => '123456',
        ]);

        $booking->locations()->createMany([
            [
                'location_type' => 'pickup',
                'latitude' => 13.0827,
                'longitude' => 80.2707,
                'address' => 'Pickup Loc',
                'sequence' => 1,
            ],
        ]);

        // Run 1st time
        $this->artisan('bookings:process-scheduled')->assertExitCode(0);
        $booking->refresh();
        $firstProcessedAt = $booking->scheduled_processed_at;

        $this->assertNotNull($firstProcessedAt);

        // Run 2nd time
        $this->artisan('bookings:process-scheduled')->assertExitCode(0);
        $booking->refresh();

        $this->assertEquals($firstProcessedAt->toDateTimeString(), $booking->scheduled_processed_at->toDateTimeString());
    }

    public function test_user_active_ride_endpoint_ignores_future_scheduled_booking(): void
    {
        $category = $this->createCategoryWithPricing();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Booking::create([
            'booking_no' => (string) \Illuminate\Support\Str::ulid(),
            'user_id' => $user->id,
            'vehicle_category_id' => $category->id,
            'service_mode' => 'scheduled',
            'scheduled_at' => now()->addHours(10),
            'status' => Booking::STATUS_SCHEDULED,
            'estimated_amount' => 100,
            'final_amount' => 0,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'start_otp' => '123456',
        ]);

        $response = $this->getJson('/api/user/bookings/check/active');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data', null);
    }
}
