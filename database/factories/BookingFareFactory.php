<?php

namespace Database\Factories;

use App\Models\BookingFare;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFareFactory extends Factory
{
    protected $model = BookingFare::class;

    public function definition(): array
    {
        return [
            'booking_id'        => null,
            'pricing_type'      => 'fixed',
            'base_fare'         => 200.00,
            'unit_rate'         => 0,
            'usage_amount'      => 1,
            'distance_charge'   => 0,
            'time_charge'       => 0,
            'waiting_charge'    => 0,
            'extra_charge'      => 0,
            'discount'          => 0,
            'subtotal'          => 200.00,
            'commission_type'   => 'percentage',
            'commission_value'  => 10.00,
            'commission_amount' => 20.00,
            'tax_percentage'    => 5.00,
            'tax_amount'        => 10.00,
            'user_total'        => 210.00,
            'driver_earnings'   => 180.00,
            'total_amount'      => 210.00,
            'distance_km'       => 5.0,
            'duration_minutes'  => 15.0,
            'waiting_minutes'   => 0,
        ];
    }
}
