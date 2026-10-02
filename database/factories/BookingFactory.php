<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'booking_no'          => 'INDBK' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'user_id'             => null,
            'driver_id'           => null,
            'vehicle_id'          => null,
            'vehicle_category_id' => null,
            'service_mode'        => 'instant',
            'start_otp'           => (string) random_int(100000, 999999),
            'status'              => Booking::STATUS_COMPLETED,
            'estimated_amount'    => 200.00,
            'final_amount'        => 200.00,
            'payment_method'      => 'cash',
            'payment_status'      => 'pending',
            'completed_at'        => now(),
        ];
    }
}
