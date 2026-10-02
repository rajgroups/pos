<?php

namespace Database\Factories;

use App\Models\VehicleCategoryPricing;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleCategoryPricingFactory extends Factory
{
    protected $model = VehicleCategoryPricing::class;

    public function definition(): array
    {
        return [
            'vehicle_category_id'    => null,
            'pricing_type'           => 'fixed',
            'base_fare'              => 100.00,
            'minimum_fare'           => 0,
            'per_km_rate'            => 0,
            'per_hour_rate'          => 0,
            'per_day_rate'           => 0,
            'per_acre_rate'          => 0,
            'per_ton_rate'           => 0,
            'waiting_charge_per_hour'=> 0,
            'night_charge_percentage'=> 0,
            'surge_multiplier'       => 1.00,
            'is_active'              => true,
            'commission_type'        => 'percentage',
            'commission_value'       => 10.00,
            'tax_percentage'         => 5.00,
        ];
    }
}
