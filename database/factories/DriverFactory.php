<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'name'           => $this->faker->name(),
            'phone'          => $this->faker->numerify('9#########'),
            'email'          => $this->faker->unique()->safeEmail(),
            'status'         => 'active',
            'is_online'      => true,
            'is_verified'    => true,
            'wallet_balance' => 0.00,
            'driver_type'    => 'car',
            'license_number' => 'DL' . $this->faker->numerify('##########'),
            'license_expiry' => now()->addYears(5)->format('Y-m-d'),
            'aadhaar_number' => $this->faker->numerify('############'),
            'pan_number'     => strtoupper($this->faker->bothify('?????####?')),
            'dob'            => $this->faker->date('Y-m-d', '-25 years'),
            'gender'         => 'male',
        ];
    }
}
