<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = VehicleType::query()
            ->whereNotNull('parent_id')
            ->pluck('id', 'slug');

        if ($categoryIds->isEmpty()) {
            return;
        }

        $driverIds = Driver::query()->pluck('id')->values();
        $now = now();

        $vehicleTemplates = [
            ['slug' => 'bike-scooter', 'brand' => 'Honda', 'model' => 'Activa 6G', 'color' => 'White', 'seating_capacity' => 2, 'load_capacity' => null, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'bike-bike', 'brand' => 'Hero', 'model' => 'Splendor Plus', 'color' => 'Black', 'seating_capacity' => 2, 'load_capacity' => null, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'bike-sports-bike', 'brand' => 'Yamaha', 'model' => 'R15 V4', 'color' => 'Blue', 'seating_capacity' => 2, 'load_capacity' => null, 'status' => 'active', 'is_verified' => false],
            ['slug' => 'car-sedan', 'brand' => 'Maruti Suzuki', 'model' => 'Dzire', 'color' => 'Silver', 'seating_capacity' => 4, 'load_capacity' => null, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'car-hatchback', 'brand' => 'Hyundai', 'model' => 'Grand i10 Nios', 'color' => 'Red', 'seating_capacity' => 4, 'load_capacity' => null, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'car-suv', 'brand' => 'Toyota', 'model' => 'Innova Crysta', 'color' => 'Grey', 'seating_capacity' => 6, 'load_capacity' => null, 'status' => 'maintenance', 'is_verified' => true],
            ['slug' => 'car-luxury', 'brand' => 'Mercedes-Benz', 'model' => 'C-Class', 'color' => 'Black', 'seating_capacity' => 4, 'load_capacity' => null, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'jeep-standard-jeep', 'brand' => 'Mahindra', 'model' => 'Bolero Neo', 'color' => 'Green', 'seating_capacity' => 6, 'load_capacity' => null, 'status' => 'active', 'is_verified' => false],
            ['slug' => 'jeep-premium-jeep', 'brand' => 'Mahindra', 'model' => 'Scorpio N', 'color' => 'White', 'seating_capacity' => 7, 'load_capacity' => null, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'jeep-off-road', 'brand' => 'Force', 'model' => 'Gurkha', 'color' => 'Brown', 'seating_capacity' => 5, 'load_capacity' => null, 'status' => 'inactive', 'is_verified' => false],
            ['slug' => 'van-traveller', 'brand' => 'Force', 'model' => 'Traveller 3350', 'color' => 'White', 'seating_capacity' => 12, 'load_capacity' => null, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'van-mini-van', 'brand' => 'Maruti Suzuki', 'model' => 'Eeco', 'color' => 'Blue', 'seating_capacity' => 8, 'load_capacity' => null, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'van-tempo', 'brand' => 'Tata', 'model' => 'Winger', 'color' => 'Silver', 'seating_capacity' => 14, 'load_capacity' => 850.00, 'status' => 'maintenance', 'is_verified' => false],
            ['slug' => 'bus-mini-bus', 'brand' => 'Tata', 'model' => 'Starbus', 'color' => 'Yellow', 'seating_capacity' => 20, 'load_capacity' => 1200.00, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'bus-standard-bus', 'brand' => 'Ashok Leyland', 'model' => 'Viking', 'color' => 'White', 'seating_capacity' => 35, 'load_capacity' => 2500.00, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'bus-luxury-coach', 'brand' => 'Volvo', 'model' => '9400', 'color' => 'Maroon', 'seating_capacity' => 45, 'load_capacity' => 3200.00, 'status' => 'active', 'is_verified' => true],
            ['slug' => 'tractor-farm-tractor', 'brand' => 'Swaraj', 'model' => '744 XT', 'color' => 'Red', 'seating_capacity' => 1, 'load_capacity' => 1800.00, 'status' => 'active', 'is_verified' => false],
            ['slug' => 'tractor-heavy-duty', 'brand' => 'John Deere', 'model' => '5310', 'color' => 'Green', 'seating_capacity' => 1, 'load_capacity' => 2500.00, 'status' => 'maintenance', 'is_verified' => true],
            ['slug' => 'tractor-mini-tractor', 'brand' => 'Kubota', 'model' => 'NeoStar A211N', 'color' => 'Orange', 'seating_capacity' => 1, 'load_capacity' => 900.00, 'status' => 'active', 'is_verified' => true],
        ];

        for ($i = 0; $i < 50; $i++) {
            $template = $vehicleTemplates[$i % count($vehicleTemplates)];
            $sequence = $i + 1;
            $year = 2017 + ($i % 8);
            $rcExpiry = Carbon::create(2028 + ($i % 3), (($i % 12) + 1), min(28, 10 + ($i % 18)));
            $insuranceExpiry = Carbon::create(2027 + ($i % 2), (($i % 12) + 1), min(28, 12 + ($i % 16)));
            $permitExpiry = Carbon::create(2028 + ($i % 4), (($i % 12) + 1), min(28, 8 + ($i % 20)));
            $fitnessExpiry = Carbon::create(2027 + ($i % 3), (($i % 12) + 1), min(28, 6 + ($i % 22)));

            DB::table('vehicles')->updateOrInsert(
                ['vehicle_number' => $this->vehicleNumber($sequence)],
                [
                    'driver_id' => $driverIds->isNotEmpty() ? $driverIds[$i % $driverIds->count()] : null,
                    'vehicle_category_id' => $categoryIds[$template['slug']] ?? null,
                    'brand' => $template['brand'],
                    'model' => $template['model'],
                    'color' => $template['color'],
                    'manufacture_year' => $year,
                    'rc_number' => sprintf('RC%04d%04d', $year, $sequence),
                    'rc_expiry' => $rcExpiry->toDateString(),
                    'insurance_number' => sprintf('INS%04d%05d', $year, $sequence),
                    'insurance_expiry' => $insuranceExpiry->toDateString(),
                    'permit_number' => sprintf('PRM%04d%04d', $year, $sequence),
                    'permit_expiry' => $permitExpiry->toDateString(),
                    'fitness_certificate_number' => sprintf('FIT%04d%04d', $year, $sequence),
                    'fitness_expiry' => $fitnessExpiry->toDateString(),
                    'seating_capacity' => $template['seating_capacity'],
                    'load_capacity' => $template['load_capacity'],
                    'front_image' => "vehicles/front/vehicle-{$sequence}.jpg",
                    'back_image' => "vehicles/back/vehicle-{$sequence}.jpg",
                    'side_image' => "vehicles/side/vehicle-{$sequence}.jpg",
                    'status' => $template['status'],
                    'is_verified' => $template['is_verified'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function vehicleNumber(int $sequence): string
    {
        $states = ['TN', 'KA', 'KL', 'AP', 'TS'];
        $series = ['AB', 'CD', 'EF', 'GH', 'JK', 'LM', 'NP', 'QR'];

        $state = $states[($sequence - 1) % count($states)];
        $district = str_pad((string) ((($sequence - 1) % 30) + 1), 2, '0', STR_PAD_LEFT);
        $letters = $series[($sequence - 1) % count($series)];
        $number = str_pad((string) (1200 + $sequence), 4, '0', STR_PAD_LEFT);

        return "{$state}{$district}{$letters}{$number}";
    }
}
