<?php

namespace Database\Seeders;

use App\Models\VehicleCategoryPricing;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $vehicleTypes = [
            [
                'type_key' => 'cab',
                'service_mode' => 'instant',
                'label' => 'Cab',
                'icon' => 'local_taxi_rounded',
                'accent_color' => '#0F766E',
                'sheet_gradient' => ['#F0FDFA', '#CCFBF1'],
                'tagline' => 'City rides and airport transfers',
                'starting_fare' => 'From Rs 40',
                'pricing' => [
                    'pricing_type' => 'distance',
                    'base_fare' => 40,
                    'minimum_fare' => 80,
                    'per_km_rate' => 12,
                ],
                'sub_categories' => [
                    ['name' => 'Mini Cab', 'price' => 'Rs 12/km', 'description' => 'Compact city rides', 'eta' => '3 mins away', 'seats' => 4],
                    ['name' => 'Sedan Cab', 'price' => 'Rs 14/km', 'description' => 'Comfortable daily travel', 'eta' => '4 mins away', 'seats' => 4],
                    ['name' => 'SUV Cab', 'price' => 'Rs 18/km', 'description' => 'More room for family trips', 'eta' => '6 mins away', 'seats' => 6],
                ],
            ],
            [
                'type_key' => 'auto',
                'service_mode' => 'instant',
                'label' => 'Auto',
                'icon' => 'emoji_transportation_rounded',
                'accent_color' => '#F97316',
                'sheet_gradient' => ['#FFF7ED', '#FFEDD5'],
                'tagline' => 'Quick local hops',
                'starting_fare' => 'From Rs 30',
                'pricing' => [
                    'pricing_type' => 'distance',
                    'base_fare' => 30,
                    'minimum_fare' => 60,
                    'per_km_rate' => 10,
                ],
                'sub_categories' => [
                    ['name' => 'Regular Auto', 'price' => 'Rs 10/km', 'description' => 'Affordable short trips', 'eta' => '2 mins away', 'seats' => 3],
                    ['name' => 'AC Auto', 'price' => 'Rs 12/km', 'description' => 'Comfort with air conditioning', 'eta' => '4 mins away', 'seats' => 3],
                ],
            ],
            [
                'type_key' => 'bike',
                'service_mode' => 'instant',
                'label' => 'Bike',
                'icon' => 'two_wheeler_rounded',
                'accent_color' => '#2563EB',
                'sheet_gradient' => ['#EFF6FF', '#DBEAFE'],
                'tagline' => 'Fast city hops',
                'starting_fare' => 'From Rs 8',
                'pricing' => [
                    'pricing_type' => 'distance',
                    'base_fare' => 15,
                    'minimum_fare' => 30,
                    'per_km_rate' => 8,
                ],
                'sub_categories' => [
                    ['name' => 'Standard Bike', 'price' => 'Rs 8/km', 'description' => 'Fast and economical', 'eta' => '2 mins away', 'seats' => 1],
                    ['name' => 'Premium Bike', 'price' => 'Rs 12/km', 'description' => 'Premium two-wheeler ride', 'eta' => '4 mins away', 'seats' => 1],
                ],
            ],
            [
                'type_key' => 'truck',
                'service_mode' => 'scheduled',
                'label' => 'Truck',
                'icon' => 'local_shipping_rounded',
                'accent_color' => '#7C2D12',
                'sheet_gradient' => ['#FEF3C7', '#FDE68A'],
                'tagline' => 'Heavy transport and logistics',
                'starting_fare' => 'From Rs 1,200/ton',
                'pricing' => [
                    'pricing_type' => 'weight',
                    'base_fare' => 300,
                    'minimum_fare' => 1200,
                    'per_ton_rate' => 1200,
                ],
                'sub_categories' => [
                    ['name' => 'Mini Truck', 'price' => 'Rs 1,200/ton', 'description' => 'Local cargo and deliveries', 'eta' => '8 mins away', 'seats' => 2],
                    ['name' => 'Open Truck', 'price' => 'Rs 1,500/ton', 'description' => 'Bulk goods movement', 'eta' => '12 mins away', 'seats' => 2],
                    ['name' => 'Container Truck', 'price' => 'Rs 2,000/ton', 'description' => 'Long-haul freight transport', 'eta' => '18 mins away', 'seats' => 2],
                ],
            ],
            [
                'type_key' => 'parcel',
                'service_mode' => 'instant',
                'label' => 'Parcel',
                'icon' => 'inventory_2_rounded',
                'accent_color' => '#0EA5E9',
                'sheet_gradient' => ['#F0F9FF', '#E0F2FE'],
                'tagline' => 'Parcel delivery and courier',
                'starting_fare' => 'From Rs 20',
                'pricing' => [
                    'pricing_type' => 'weight',
                    'base_fare' => 20,
                    'minimum_fare' => 50,
                    'per_ton_rate' => 350,
                ],
                'sub_categories' => [
                    ['name' => 'Document Delivery', 'price' => 'Rs 50 minimum', 'description' => 'Same city documents', 'eta' => '30 mins away'],
                    ['name' => 'Parcel Delivery', 'price' => 'Rs 350/ton', 'description' => 'Small parcels and packages', 'eta' => '45 mins away'],
                ],
            ],
            [
                'type_key' => 'jcb',
                'service_mode' => 'scheduled',
                'label' => 'JCB',
                'icon' => 'engineering_rounded',
                'accent_color' => '#CA8A04',
                'sheet_gradient' => ['#FFFBEB', '#FEF3C7'],
                'tagline' => 'Construction and earthwork rental',
                'starting_fare' => 'From Rs 750/hr',
                'pricing' => [
                    'pricing_type' => 'hourly',
                    'base_fare' => 500,
                    'minimum_fare' => 1500,
                    'per_hour_rate' => 750,
                ],
                'sub_categories' => [
                    ['name' => 'Backhoe Loader', 'price' => 'Rs 750/hr', 'description' => 'Standard construction support', 'eta' => '20 mins away'],
                    ['name' => 'Excavator', 'price' => 'Rs 950/hr', 'description' => 'Heavy digging and loading', 'eta' => '25 mins away'],
                ],
            ],
            [
                'type_key' => 'crane',
                'service_mode' => 'scheduled',
                'label' => 'Crane',
                'icon' => 'precision_manufacturing_rounded',
                'accent_color' => '#7E22CE',
                'sheet_gradient' => ['#FAF5FF', '#F3E8FF'],
                'tagline' => 'Lifting and site support',
                'starting_fare' => 'From Rs 1,250/hr',
                'pricing' => [
                    'pricing_type' => 'hourly',
                    'base_fare' => 800,
                    'minimum_fare' => 2000,
                    'per_hour_rate' => 1250,
                ],
                'sub_categories' => [
                    ['name' => 'Mobile Crane', 'price' => 'Rs 1,250/hr', 'description' => 'Flexible lifting support', 'eta' => '30 mins away'],
                    ['name' => 'Heavy Crane', 'price' => 'Rs 1,800/hr', 'description' => 'Large lift and site work', 'eta' => '40 mins away'],
                ],
            ],
            [
                'type_key' => 'harvester',
                'service_mode' => 'scheduled',
                'label' => 'Harvester',
                'icon' => 'agriculture_rounded',
                'accent_color' => '#15803D',
                'sheet_gradient' => ['#F0FDF4', '#DCFCE7'],
                'tagline' => 'Crop harvesting on demand',
                'starting_fare' => 'From Rs 1,800/acre',
                'pricing' => [
                    'pricing_type' => 'acre',
                    'base_fare' => 1000,
                    'minimum_fare' => 1800,
                    'per_acre_rate' => 1800,
                ],
                'sub_categories' => [
                    ['name' => 'Combine Harvester', 'price' => 'Rs 1,800/acre', 'description' => 'Mixed crop harvesting', 'eta' => '45 mins away'],
                    ['name' => 'Rice Harvester', 'price' => 'Rs 2,000/acre', 'description' => 'Specialized paddy harvesting', 'eta' => '60 mins away'],
                ],
            ],
            [
                'type_key' => 'tractor',
                'service_mode' => 'scheduled',
                'label' => 'Tractor',
                'icon' => 'agriculture_rounded',
                'accent_color' => '#166534',
                'sheet_gradient' => ['#ECFDF5', '#D1FAE5'],
                'tagline' => 'Farm and utility rental',
                'starting_fare' => 'From Rs 300/hr',
                'pricing' => [
                    'pricing_type' => 'daily',
                    'base_fare' => 900,
                    'minimum_fare' => 1800,
                    'per_day_rate' => 2400,
                ],
                'sub_categories' => [
                    ['name' => 'Mini Tractor', 'price' => 'Rs 300/hr', 'description' => 'Compact work and haulage', 'eta' => '15 mins away'],
                    ['name' => 'Farm Tractor', 'price' => 'Rs 2,400/day', 'description' => 'Daily agricultural rental', 'eta' => '20 mins away'],
                ],
            ],
        ];

        foreach ($vehicleTypes as $index => $vehicleType) {
            $parent = VehicleType::updateOrCreate(
                ['slug' => Str::slug($vehicleType['label'])],
                [
                    'parent_id' => null,
                    'type_key' => $vehicleType['type_key'],
                    'service_mode' => $vehicleType['service_mode'] ?? 'instant',
                    'name' => $vehicleType['label'],
                    'icon' => $vehicleType['icon'],
                    'accent_color' => $vehicleType['accent_color'],
                    'gradient_start' => $vehicleType['sheet_gradient'][0] ?? null,
                    'gradient_end' => $vehicleType['sheet_gradient'][1] ?? null,
                    'tagline' => $vehicleType['tagline'],
                    'starting_fare' => $vehicleType['starting_fare'],
                    'price_label' => null,
                    'eta' => null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );

            VehicleCategoryPricing::updateOrCreate(
                ['vehicle_category_id' => $parent->id],
                [
                    'pricing_type' => $vehicleType['pricing']['pricing_type'],
                    'base_fare' => $vehicleType['pricing']['base_fare'] ?? 0,
                    'minimum_fare' => $vehicleType['pricing']['minimum_fare'] ?? 0,
                    'per_km_rate' => $vehicleType['pricing']['per_km_rate'] ?? 0,
                    'per_hour_rate' => $vehicleType['pricing']['per_hour_rate'] ?? 0,
                    'per_day_rate' => $vehicleType['pricing']['per_day_rate'] ?? 0,
                    'per_acre_rate' => $vehicleType['pricing']['per_acre_rate'] ?? 0,
                    'per_ton_rate' => $vehicleType['pricing']['per_ton_rate'] ?? 0,
                    'waiting_charge_per_hour' => $vehicleType['pricing']['waiting_charge_per_hour'] ?? 0,
                    'night_charge_percentage' => $vehicleType['pricing']['night_charge_percentage'] ?? 0,
                    'surge_multiplier' => $vehicleType['pricing']['surge_multiplier'] ?? 1,
                    'is_active' => true,
                ]
            );

            foreach ($vehicleType['sub_categories'] as $subIndex => $subCategory) {
                VehicleType::updateOrCreate(
                    ['slug' => Str::slug($vehicleType['type_key'] . '-' . $subCategory['name'])],
                    [
                        'parent_id' => $parent->id,
                        'type_key' => $vehicleType['type_key'],
                        'name' => $subCategory['name'],
                        'description' => $subCategory['description'],
                        'price_label' => $subCategory['price'],
                        'eta' => $subCategory['eta'],
                        'max_capacity' => $subCategory['seats'] ?? null,
                        'sort_order' => $subIndex + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
