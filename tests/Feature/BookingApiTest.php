<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategoryPricing;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_categories_can_be_listed(): void
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

        $response = $this->getJson('/api/user/vehicle-categories');

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.0.name', 'Cab');
    }

    public function test_booking_lifecycle_works_end_to_end(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

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
            'status' => 'active',
            'is_verified' => true,
        ]);

        $vehicle = Vehicle::create([
            'vehicle_category_id' => $category->id,
            'vehicle_number' => 'TN01AB9999',
            'status' => 'active',
            'is_verified' => true,
        ]);

        $summaryResponse = $this->postJson('/api/user/bookings/fare-summary', [
            'vehicle_category_id' => $category->id,
            'usage' => ['distance_km' => 10],
        ]);

        $summaryResponse->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.total_amount', 160);

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

        $storeResponse->assertCreated()
            ->assertJsonPath('status', true);

        $bookingNo = $storeResponse->json('data.booking_no');
        $booking = Booking::where('booking_no', $bookingNo)->firstOrFail();

        $this->postJson("/api/user/bookings/{$bookingNo}/accept", [
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
        ])->assertOk()
            ->assertJsonPath('data.status', 'accepted');

        $booking->refresh();

        $this->postJson("/api/user/bookings/{$bookingNo}/start", [
            'start_otp' => $booking->start_otp,
        ])->assertOk()
            ->assertJsonPath('data.status', 'started');

        $this->postJson("/api/user/bookings/{$bookingNo}/complete", [
            'final_amount' => 180,
            'payment_status' => 'paid',
        ])->assertOk()
            ->assertJsonPath('data.status', 'completed');
    }
}
