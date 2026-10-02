<?php

namespace Database\Factories;

use App\Models\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleCategoryFactory extends Factory
{
    protected $model = VehicleCategory::class;

    public function definition(): array
    {
        return [
            'name'                  => $this->faker->randomElement(['Sedan', 'SUV', 'Hatchback', 'Auto', 'Bike']),
            'slug'                  => $this->faker->unique()->slug(2),
            'service_mode'          => 'instant',
            'drop_location_required'=> true,
            'is_active'             => true,
        ];
    }
}
