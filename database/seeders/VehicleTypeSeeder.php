<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $vehicleTypes = [
            [
                'type_key' => 'bike',
                'label' => 'Bike',
                'icon' => 'two_wheeler_rounded',
                'accent_color' => '#2563EB',
                'sheet_gradient' => ['#F4F8FF', '#E8F0FF'],
                'tagline' => 'Fast city hops',
                'starting_fare' => 'From Rs 8/km',
                'sub_categories' => [
                    [
                        'name' => 'Scooter',
                        'price' => 'Rs 8/km',
                        'description' => 'Best for short city pickups',
                        'eta' => '2 mins away',
                    ],
                    [
                        'name' => 'Bike',
                        'price' => 'Rs 10/km',
                        'description' => 'Quick rides with low wait time',
                        'eta' => '3 mins away',
                    ],
                    [
                        'name' => 'Sports Bike',
                        'price' => 'Rs 15/km',
                        'description' => 'Premium two-wheeler experience',
                        'eta' => '5 mins away',
                    ],
                ],
            ],
            [
                'type_key' => 'car',
                'label' => 'Car',
                'icon' => 'directions_car_filled_rounded',
                'accent_color' => '#0F766E',
                'sheet_gradient' => ['#F1FFFD', '#E2FAF6'],
                'tagline' => 'Comfort everyday ride',
                'starting_fare' => 'From Rs 10/km',
                'sub_categories' => [
                    [
                        'name' => 'Sedan',
                        'price' => 'Rs 12/km',
                        'description' => 'Comfort ride for daily travel',
                        'eta' => '4 mins away',
                        'seats' => 4,
                    ],
                    [
                        'name' => 'Hatchback',
                        'price' => 'Rs 10/km',
                        'description' => 'Affordable and compact',
                        'eta' => '3 mins away',
                        'seats' => 4,
                    ],
                    [
                        'name' => 'SUV',
                        'price' => 'Rs 18/km',
                        'description' => 'Extra room for family trips',
                        'eta' => '6 mins away',
                        'seats' => 6,
                    ],
                    [
                        'name' => 'Luxury',
                        'price' => 'Rs 25/km',
                        'description' => 'Executive comfort and premium driver',
                        'eta' => '8 mins away',
                        'seats' => 4,
                    ],
                ],
            ],
            [
                'type_key' => 'jeep',
                'label' => 'Jeep',
                'icon' => 'airport_shuttle_rounded',
                'accent_color' => '#7C3AED',
                'sheet_gradient' => ['#F7F2FF', '#EEE5FF'],
                'tagline' => 'Spacious and rugged',
                'starting_fare' => 'From Rs 20/km',
                'sub_categories' => [
                    [
                        'name' => 'Standard Jeep',
                        'price' => 'Rs 20/km',
                        'description' => 'Reliable for mixed road conditions',
                        'eta' => '5 mins away',
                        'seats' => 6,
                    ],
                    [
                        'name' => 'Premium Jeep',
                        'price' => 'Rs 28/km',
                        'description' => 'Spacious premium cabin',
                        'eta' => '7 mins away',
                        'seats' => 7,
                    ],
                    [
                        'name' => 'Off-Road',
                        'price' => 'Rs 30/km',
                        'description' => 'Built for rough terrain',
                        'eta' => '9 mins away',
                        'seats' => 5,
                    ],
                ],
            ],
            [
                'type_key' => 'van',
                'label' => 'Van',
                'icon' => 'local_shipping_rounded',
                'accent_color' => '#DC6803',
                'sheet_gradient' => ['#FFF7ED', '#FFEAD5'],
                'tagline' => 'For groups and luggage',
                'starting_fare' => 'From Rs 18/km',
                'sub_categories' => [
                    [
                        'name' => 'Traveller',
                        'price' => 'Rs 22/km',
                        'description' => 'Ideal for group transfers',
                        'eta' => '8 mins away',
                        'seats' => 12,
                    ],
                    [
                        'name' => 'Mini Van',
                        'price' => 'Rs 18/km',
                        'description' => 'Flexible for luggage and guests',
                        'eta' => '6 mins away',
                        'seats' => 8,
                    ],
                    [
                        'name' => 'Tempo',
                        'price' => 'Rs 25/km',
                        'description' => 'Large people mover',
                        'eta' => '10 mins away',
                        'seats' => 14,
                    ],
                ],
            ],
            [
                'type_key' => 'bus',
                'label' => 'Bus',
                'icon' => 'directions_bus_rounded',
                'accent_color' => '#BE123C',
                'sheet_gradient' => ['#FFF1F2', '#FFE4E6'],
                'tagline' => 'Team and event travel',
                'starting_fare' => 'From Rs 30/km',
                'sub_categories' => [
                    [
                        'name' => 'Mini Bus',
                        'price' => 'Rs 30/km',
                        'description' => 'Efficient for team movement',
                        'eta' => '12 mins away',
                        'seats' => 20,
                    ],
                    [
                        'name' => 'Standard Bus',
                        'price' => 'Rs 40/km',
                        'description' => 'Balanced capacity and comfort',
                        'eta' => '14 mins away',
                        'seats' => 35,
                    ],
                    [
                        'name' => 'Luxury Coach',
                        'price' => 'Rs 55/km',
                        'description' => 'Premium intercity or event travel',
                        'eta' => '18 mins away',
                        'seats' => 45,
                    ],
                ],
            ],
            [
                'type_key' => 'tractor',
                'label' => 'Tractor',
                'icon' => 'agriculture_rounded',
                'accent_color' => '#15803D',
                'sheet_gradient' => ['#F0FDF4', '#DCFCE7'],
                'tagline' => 'Field and heavy utility',
                'starting_fare' => 'From Rs 350/hr',
                'sub_categories' => [
                    [
                        'name' => 'Farm Tractor',
                        'price' => 'Rs 500/hr',
                        'description' => 'Routine farm and field support',
                        'eta' => '15 mins away',
                    ],
                    [
                        'name' => 'Heavy Duty',
                        'price' => 'Rs 800/hr',
                        'description' => 'For tough hauling and work sites',
                        'eta' => '20 mins away',
                    ],
                    [
                        'name' => 'Mini Tractor',
                        'price' => 'Rs 350/hr',
                        'description' => 'Compact utility for smaller jobs',
                        'eta' => '11 mins away',
                    ],
                ],
            ],
        ];

        foreach ($vehicleTypes as $index => $vehicleType) {
            $parent = VehicleType::updateOrCreate(
                ['slug' => Str::slug($vehicleType['label'])],
                [
                    'parent_id' => null,
                    'type_key' => $vehicleType['type_key'],
                    'name' => $vehicleType['label'],
                    'icon' => $vehicleType['icon'],
                    'accent_color' => $vehicleType['accent_color'],
                    'gradient_start' => $vehicleType['sheet_gradient'][0] ?? null,
                    'gradient_end' => $vehicleType['sheet_gradient'][1] ?? null,
                    'tagline' => $vehicleType['tagline'],
                    'starting_fare' => $vehicleType['starting_fare'],
                    'sort_order' => $index + 1,
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
