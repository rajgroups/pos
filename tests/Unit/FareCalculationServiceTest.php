<?php

namespace Tests\Unit;

use App\Models\VehicleCategory;
use App\Models\VehicleCategoryPricing;
use App\Services\FareCalculationService;
use Tests\TestCase;

class FareCalculationServiceTest extends TestCase
{
    public function test_distance_pricing_uses_only_distance_charge_with_a_minimum_fare_floor(): void
    {
        $category = new VehicleCategory([
            'id' => 2,
            'name' => 'Mini Cab',
        ]);

        $category->setRelation('pricing', new VehicleCategoryPricing([
            'id' => 1,
            'pricing_type' => 'distance',
            'base_fare' => 40,
            'minimum_fare' => 80,
            'minimum_distance_km' => 3,
            'per_km_rate' => 12,
            'surge_multiplier' => 1,
            'tax_percentage' => 0,
            'commission_type' => 'percentage',
            'commission_value' => 0,
        ]));

        $service = new FareCalculationService();

        foreach ([
            [0.0, 80.00],
            [2.0, 80.00],
            [4.0, 80.00],
            [6.0, 80.00],
            [6.02, 80.00],
            [9.0, 108.00],
            [9.832, 117.98],
            [10.0, 120.00],
            [16.62, 199.44],
            [64.78, 777.36],
        ] as [$distanceKm, $expectedFare]) {
            $fare = $service->calculate($category, ['distance_km' => $distanceKm]);

            $this->assertSame($expectedFare, $fare['user_total']);
            $this->assertSame(40.00, $fare['base_fare']);
        }
    }
}
