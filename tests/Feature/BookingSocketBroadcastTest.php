<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategoryPricing;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingSocketBroadcastTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            '*' => Http::response('success', 200),
        ]);

        Redis::shouldReceive('set')->byDefault()->andReturnTrue();
        Redis::shouldReceive('del')->byDefault()->andReturn(1);
        Redis::shouldReceive('georadius')->byDefault()->andReturn([]);
    }

    public function test_accept_start_and_complete_broadcast_to_driver_and_user_sockets(): void
    {
        [$user, $driver, $vehicle, $category] = $this->createBookingContext();

        Sanctum::actingAs($user);
        $storeResponse = $this->postJson('/api/user/bookings', $this->bookingPayload($category->id));

        $storeResponse->assertCreated();

        $booking = Booking::where('booking_no', $storeResponse->json('data.booking_no'))
            ->firstOrFail();

        Sanctum::actingAs($driver);

        $this->postJson("/api/driver/bookings/{$booking->booking_no}/accept", [
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        Http::assertSentCount(2);
        Http::assertSent($this->bookingStatusAssertion($user->id, $driver->id, 'accepted'));

        $booking->refresh();

        $this->postJson("/api/driver/bookings/{$booking->booking_no}/start", [
            'start_otp' => $booking->start_otp,
        ])->assertOk()
            ->assertJsonPath('data.status', 'started');

        Http::assertSentCount(3);
        Http::assertSent($this->bookingStatusAssertion($user->id, $driver->id, 'started'));

        $booking->refresh();

        $this->postJson("/api/driver/bookings/{$booking->booking_no}/complete", [
            'end_otp' => $booking->start_otp,
            'final_amount' => 180,
            'payment_status' => 'paid',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed');

        Http::assertSentCount(4);
        Http::assertSent($this->bookingStatusAssertion($user->id, $driver->id, 'completed'));
    }

    public function test_user_cancel_broadcasts_to_driver_and_user_sockets(): void
    {
        [$user, $driver, $vehicle, $category] = $this->createBookingContext();

        Sanctum::actingAs($user);
        $storeResponse = $this->postJson('/api/user/bookings', $this->bookingPayload($category->id));

        $storeResponse->assertCreated();

        $booking = Booking::where('booking_no', $storeResponse->json('data.booking_no'))
            ->firstOrFail();

        Sanctum::actingAs($driver);

        $this->postJson("/api/driver/bookings/{$booking->booking_no}/accept", [
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
        ])->assertOk();

        Sanctum::actingAs($user);

        $this->postJson("/api/user/bookings/{$booking->booking_no}/cancel", [
            'reason' => 'Changed plans',
        ])->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        Http::assertSentCount(3);
        Http::assertSent($this->bookingStatusAssertion($user->id, $driver->id, 'cancelled'));
    }

    private function createBookingContext(): array
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
            'driver_id' => $driver->id,
            'vehicle_category_id' => $category->id,
            'vehicle_number' => 'TN01AB9999',
            'status' => 'active',
            'is_verified' => true,
        ]);

        $user = User::factory()->create();

        return [$user, $driver, $vehicle, $category];
    }

    private function bookingPayload(int $categoryId): array
    {
        return [
            'vehicle_category_id' => $categoryId,
            'booking_mode' => 'instant',
            'payment_method' => 'cash',
            'locations' => [
                [
                    'location_type' => 'pickup',
                    'latitude' => 13.0827,
                    'longitude' => 80.2707,
                    'address' => 'Chennai Central Railway Station',
                    'sequence' => 1,
                ],
                [
                    'location_type' => 'drop',
                    'latitude' => 13.0674,
                    'longitude' => 80.2376,
                    'address' => 'T Nagar, Chennai',
                    'sequence' => 2,
                ],
            ],
            'usage' => [
                'distance_km' => 15,
                'hours_used' => 1.5,
            ],
        ];
    }

    private function bookingStatusAssertion(int $userId, int $driverId, string $status): \Closure
    {
        return function (Request $request) use ($userId, $driverId, $status): bool {
            if (! str_contains($request->url(), '/broadcast-booking-update')) {
                return false;
            }

            $data = $request->data();

            return ($data['type'] ?? null) === 'booking_status'
                && ($data['booking']['status'] ?? null) === $status
                && (int) ($data['booking']['user_id'] ?? 0) === $userId
                && (int) ($data['booking']['driver_id'] ?? 0) === $driverId;
        };
    }
}
