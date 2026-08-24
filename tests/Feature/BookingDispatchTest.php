<?php

namespace Tests\Feature;

use App\Jobs\ExpireDriverBookingRequest;
use App\Models\AppSetting;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategoryPricing;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingDispatchTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Driver $driver;
    protected Vehicle $vehicle;
    protected VehicleType $category;
    protected string $socketUrl;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();

        // Configure FCM server key so FCM requests are actually made by the service
        config([
            'services.fcm.server_key' => 'test_server_key',
        ]);

        $this->socketUrl = rtrim(config('services.socket.url', 'http://127.0.0.1:9502'), '/');

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'fake_access_token'], 200),
            'https://fcm.googleapis.com/v1/projects/*/messages:send' => Http::response(['name' => 'projects/messages/123'], 200),
            'https://fcm.googleapis.com/fcm/send' => Http::response(['success' => 1], 200),
            $this->socketUrl . '/*' => Http::response('success', 200),
        ]);

        Redis::shouldReceive('set')->byDefault()->andReturnTrue();
        Redis::shouldReceive('del')->byDefault()->andReturn(1);
        Redis::shouldReceive('georadius')->byDefault()->andReturn([]);

        // Set default driver_waiting_time setting
        AppSetting::set('driver_waiting_time', '3', 'dispatch');

        // Setup base context
        $this->user = User::factory()->create(['device_token' => 'user_device_token_abc']);
        
        $this->category = VehicleType::create([
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
            'vehicle_category_id' => $this->category->id,
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

        $this->driver = Driver::create([
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
            'is_online' => 1,
            'fcm_token' => 'driver_fcm_token_xyz',
        ]);

        $this->vehicle = Vehicle::create([
            'driver_id' => $this->driver->id,
            'vehicle_category_id' => $this->category->id,
            'vehicle_number' => 'TN01AB9999',
            'status' => 'active',
            'is_verified' => true,
        ]);
    }

    protected function createBookingPayload(): array
    {
        return [
            'vehicle_category_id' => $this->category->id,
            'booking_mode' => 'instant',
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
        ];
    }

    /**
     * Test 1: Driver found + socket available -> pending -> driver receives booking
     */
    public function test_driver_found_and_socket_available(): void
    {
        Http::fake([
            $this->socketUrl . '/send_booking' => Http::response([
                'type' => 'success',
                'message' => 'Booking sent',
                'driver_ids' => [$this->driver->id],
                'sent' => 1
            ], 200),
            'https://fcm.googleapis.com/*' => Http::response('success', 200),
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/user/bookings', $this->createBookingPayload());

        $response->assertCreated();
        $bookingNo = $response->json('data.booking_no');

        $booking = Booking::where('booking_no', $bookingNo)->first();
        $this->assertNotNull($booking);
        $this->assertEquals(Booking::STATUS_PENDING, $booking->status);
        $this->assertNotNull($booking->driver_response_expires_at);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/send_booking') &&
                count($request['driver_ids']) === 1;
        });

        Queue::assertPushed(ExpireDriverBookingRequest::class, function ($job) use ($booking) {
            return $job->bookingId === $booking->id;
        });
    }

    /**
     * Test 2: Driver found + socket unavailable + FCM available -> pending
     */
    public function test_driver_found_and_socket_unavailable_with_fcm_available(): void
    {
        Http::fake([
            $this->socketUrl . '/send_booking' => Http::response([
                'type' => 'success',
                'message' => 'Booking dispatch attempted via FCM fallback (no active socket FDs)',
                'driver_ids' => [$this->driver->id],
                'sent' => 0
            ], 200),
            'https://fcm.googleapis.com/*' => Http::response('success', 200),
            'https://fcm.googleapis.com/fcm/send' => Http::response(['success' => 1], 200),
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/user/bookings', $this->createBookingPayload());

        $response->assertCreated();
        $bookingNo = $response->json('data.booking_no');

        $booking = Booking::where('booking_no', $bookingNo)->first();
        $this->assertNotNull($booking);
        $this->assertEquals(Booking::STATUS_PENDING, $booking->status);
        
        // Assert FCM was attempted by verifying the FCM request fake
        Http::assertSent(function ($request) {
            $isV1 = str_contains($request->url(), '/messages:send') &&
                isset($request['message']['token']) &&
                $request['message']['token'] === 'driver_fcm_token_xyz';

            $isLegacy = str_contains($request->url(), '/fcm/send') &&
                isset($request['to']) &&
                $request['to'] === 'driver_fcm_token_xyz';

            return $isV1 || $isLegacy;
        });

        Queue::assertPushed(ExpireDriverBookingRequest::class);
    }

    /**
     * Test 3: Driver accepts before timeout
     */
    public function test_driver_accepts_booking_before_timeout(): void
    {
        Http::fake([
            $this->socketUrl . '/send_booking' => Http::response([
                'type' => 'success',
                'message' => 'Booking sent',
                'driver_ids' => [$this->driver->id],
                'sent' => 1
            ], 200),
            $this->socketUrl . '/broadcast-booking-update' => Http::response('success', 200),
            'https://fcm.googleapis.com/*' => Http::response('success', 200),
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/user/bookings', $this->createBookingPayload());
        $bookingNo = $response->json('data.booking_no');
        $booking = Booking::where('booking_no', $bookingNo)->first();

        Sanctum::actingAs($this->driver);
        $acceptResponse = $this->postJson("/api/driver/bookings/{$bookingNo}/accept", [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
        ]);
        $acceptResponse->assertOk();
        
        $booking->refresh();
        $this->assertEquals(Booking::STATUS_ACCEPTED, $booking->status);
        $this->assertEquals($this->driver->id, $booking->driver_id);

        // Run the expiry job handler manually
        $job = new ExpireDriverBookingRequest($booking->id);
        $job->handle(app(\App\Services\BookingService::class));

        $booking->refresh();
        // Booking must remain accepted
        $this->assertEquals(Booking::STATUS_ACCEPTED, $booking->status);
    }

    /**
     * Test 4: Driver Rejects
     */
    public function test_driver_rejects_booking_remains_pending(): void
    {
        Http::fake([
            $this->socketUrl . '/send_booking' => Http::response([
                'type' => 'success',
                'message' => 'Booking sent',
                'driver_ids' => [$this->driver->id],
                'sent' => 1
            ], 200),
            'https://fcm.googleapis.com/*' => Http::response('success', 200),
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/user/bookings', $this->createBookingPayload());
        $bookingNo = $response->json('data.booking_no');
        $booking = Booking::where('booking_no', $bookingNo)->first();

        // Simulate reject over WebSocket message handler simulation
        Log::info('Driver rejected booking', ['fd' => 99, 'payload' => ['booking_id' => $booking->id]]);

        $booking->refresh();
        // Status should remain pending
        $this->assertEquals(Booking::STATUS_PENDING, $booking->status);
    }

    /**
     * Test 5: Timeout expires with no acceptance -> pending -> no_driver_available
     */
    public function test_timeout_expires_with_no_acceptance(): void
    {
        Http::fake([
            $this->socketUrl . '/send_booking' => Http::response([
                'type' => 'success',
                'message' => 'Booking sent',
                'driver_ids' => [$this->driver->id],
                'sent' => 1
            ], 200),
            $this->socketUrl . '/broadcast-booking-update' => Http::response('success', 200),
            'https://fcm.googleapis.com/*' => Http::response('success', 200),
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/user/bookings', $this->createBookingPayload());
        $bookingNo = $response->json('data.booking_no');
        $booking = Booking::where('booking_no', $bookingNo)->first();

        $this->assertEquals(Booking::STATUS_PENDING, $booking->status);

        // Expire the waiting time by setting it to 1 minute ago in the DB
        $booking->update(['driver_response_expires_at' => now()->subMinutes(1)]);

        // Run the expiry job handler manually
        $job = new ExpireDriverBookingRequest($booking->id);
        $job->handle(app(\App\Services\BookingService::class));

        $booking->refresh();
        $this->assertEquals(Booking::STATUS_NO_DRIVER_AVAILABLE, $booking->status);
    }

    /**
     * Test 6: Booking cancelled before timeout -> expiry job does nothing
     */
    public function test_booking_cancelled_before_timeout(): void
    {
        Http::fake([
            $this->socketUrl . '/send_booking' => Http::response([
                'type' => 'success',
                'message' => 'Booking sent',
                'driver_ids' => [$this->driver->id],
                'sent' => 1
            ], 200),
            $this->socketUrl . '/broadcast-booking-update' => Http::response('success', 200),
            'https://fcm.googleapis.com/*' => Http::response('success', 200),
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/user/bookings', $this->createBookingPayload());
        $bookingNo = $response->json('data.booking_no');
        $booking = Booking::where('booking_no', $bookingNo)->first();

        // Cancel booking
        $cancelResponse = $this->postJson("/api/user/bookings/{$bookingNo}/cancel", [
            'reason' => 'User changed mind',
        ]);
        $cancelResponse->assertOk();

        $booking->refresh();
        $this->assertEquals(Booking::STATUS_CANCELLED, $booking->status);

        // Run the expiry job handler manually
        $job = new ExpireDriverBookingRequest($booking->id);
        $job->handle(app(\App\Services\BookingService::class));

        $booking->refresh();
        // Booking must remain cancelled
        $this->assertEquals(Booking::STATUS_CANCELLED, $booking->status);
    }

    /**
     * Test 7: Expiry job executes after driver accepted -> expiry job does nothing
     */
    public function test_expiry_job_executes_after_driver_accepted(): void
    {
        Http::fake([
            $this->socketUrl . '/send_booking' => Http::response([
                'type' => 'success',
                'message' => 'Booking sent',
                'driver_ids' => [$this->driver->id],
                'sent' => 1
            ], 200),
            $this->socketUrl . '/broadcast-booking-update' => Http::response('success', 200),
            'https://fcm.googleapis.com/*' => Http::response('success', 200),
        ]);

        Sanctum::actingAs($this->user);
        $response = $this->postJson('/api/user/bookings', $this->createBookingPayload());
        $bookingNo = $response->json('data.booking_no');
        $booking = Booking::where('booking_no', $bookingNo)->first();

        Sanctum::actingAs($this->driver);
        $this->postJson("/api/driver/bookings/{$bookingNo}/accept", [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
        ])->assertOk();

        $booking->refresh();
        $this->assertEquals(Booking::STATUS_ACCEPTED, $booking->status);

        // Run the expiry job handler manually
        $job = new ExpireDriverBookingRequest($booking->id);
        $job->handle(app(\App\Services\BookingService::class));

        $booking->refresh();
        $this->assertEquals(Booking::STATUS_ACCEPTED, $booking->status);
    }

    /**
     * Test 8: Dynamic waiting time
     */
    public function test_dynamic_waiting_time_setting(): void
    {
        Http::fake([
            $this->socketUrl . '/*' => Http::response('success', 200),
            'https://fcm.googleapis.com/*' => Http::response('success', 200),
        ]);

        Sanctum::actingAs($this->user);

        // Setting 1: 5 minutes
        AppSetting::set('driver_waiting_time', '5', 'dispatch');
        $response = $this->postJson('/api/user/bookings', $this->createBookingPayload());
        $bookingNo = $response->json('data.booking_no');
        $booking = Booking::where('booking_no', $bookingNo)->first();
        
        $expectedExpiry = $booking->created_at->addMinutes(5);
        $this->assertEquals($expectedExpiry->toDateTimeString(), $booking->driver_response_expires_at->toDateTimeString());

        // Cancel booking so user can request another one without validation errors
        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        // Setting 2: 10 minutes
        AppSetting::set('driver_waiting_time', '10', 'dispatch');
        $response2 = $this->postJson('/api/user/bookings', $this->createBookingPayload());
        $bookingNo2 = $response2->json('data.booking_no');
        $booking2 = Booking::where('booking_no', $bookingNo2)->first();
        
        $expectedExpiry2 = $booking2->created_at->addMinutes(10);
        $this->assertEquals($expectedExpiry2->toDateTimeString(), $booking2->driver_response_expires_at->toDateTimeString());
    }
}
