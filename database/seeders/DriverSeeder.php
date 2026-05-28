<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $drivers = [
            ['name' => 'Arun Kumar', 'phone' => '9000000001', 'email' => 'arun.driver@example.com', 'city' => 'Chennai', 'state' => 'Tamil Nadu', 'driver_type' => 'car', 'license_categories' => ['LMV', 'TR'], 'status' => 'active', 'is_verified' => true],
            ['name' => 'Bala Murugan', 'phone' => '9000000002', 'email' => 'bala.driver@example.com', 'city' => 'Madurai', 'state' => 'Tamil Nadu', 'driver_type' => 'bike', 'license_categories' => ['Bike'], 'status' => 'active', 'is_verified' => true],
            ['name' => 'Chetan Raj', 'phone' => '9000000003', 'email' => 'chetan.driver@example.com', 'city' => 'Bengaluru', 'state' => 'Karnataka', 'driver_type' => 'mini_van', 'license_categories' => ['LMV', 'TR'], 'status' => 'active', 'is_verified' => false],
            ['name' => 'Dinesh Kumar', 'phone' => '9000000004', 'email' => 'dinesh.driver@example.com', 'city' => 'Coimbatore', 'state' => 'Tamil Nadu', 'driver_type' => 'bus', 'license_categories' => ['HMV', 'TR'], 'status' => 'inactive', 'is_verified' => true],
            ['name' => 'Elango', 'phone' => '9000000005', 'email' => 'elango.driver@example.com', 'city' => 'Salem', 'state' => 'Tamil Nadu', 'driver_type' => 'tractor', 'license_categories' => ['TR', 'Tractor'], 'status' => 'active', 'is_verified' => true],
            ['name' => 'Farooq Ahmed', 'phone' => '9000000006', 'email' => 'farooq.driver@example.com', 'city' => 'Hyderabad', 'state' => 'Telangana', 'driver_type' => 'car', 'license_categories' => ['LMV'], 'status' => 'active', 'is_verified' => false],
            ['name' => 'Gokul Prasad', 'phone' => '9000000007', 'email' => 'gokul.driver@example.com', 'city' => 'Kochi', 'state' => 'Kerala', 'driver_type' => 'bus', 'license_categories' => ['HMV', 'TR'], 'status' => 'active', 'is_verified' => true],
            ['name' => 'Hariharan', 'phone' => '9000000008', 'email' => 'hari.driver@example.com', 'city' => 'Trichy', 'state' => 'Tamil Nadu', 'driver_type' => 'bike', 'license_categories' => ['Bike', 'LMV'], 'status' => 'blocked', 'is_verified' => false],
            ['name' => 'Imran Khan', 'phone' => '9000000009', 'email' => 'imran.driver@example.com', 'city' => 'Vijayawada', 'state' => 'Andhra Pradesh', 'driver_type' => 'car', 'license_categories' => ['LMV', 'TR'], 'status' => 'active', 'is_verified' => true],
            ['name' => 'Jeeva', 'phone' => '9000000010', 'email' => 'jeeva.driver@example.com', 'city' => 'Erode', 'state' => 'Tamil Nadu', 'driver_type' => 'tractor', 'license_categories' => ['TR', 'Tractor'], 'status' => 'active', 'is_verified' => true],
        ];

        foreach ($drivers as $index => $driver) {
            Driver::query()->updateOrCreate(
                ['phone' => $driver['phone']],
                [
                    'name' => $driver['name'],
                    'email' => $driver['email'],
                    'dob' => now()->subYears(28 + $index)->toDateString(),
                    'gender' => $index % 3 === 0 ? 'male' : ($index % 3 === 1 ? 'female' : 'other'),
                    'address' => 'No. ' . (12 + $index) . ', Transport Nagar',
                    'city' => $driver['city'],
                    'state' => $driver['state'],
                    'pincode' => '6000' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'aadhaar_number' => '4567' . str_pad((string) (89010000 + $index), 8, '0', STR_PAD_LEFT),
                    'pan_number' => 'DRVPK' . str_pad((string) (1000 + $index), 4, '0', STR_PAD_LEFT) . 'Z',
                    'license_number' => 'DL' . str_pad((string) (20260000 + $index + 1), 8, '0', STR_PAD_LEFT),
                    'license_expiry' => now()->addYears(3 + ($index % 4))->toDateString(),
                    'license_categories' => $driver['license_categories'],
                    'driver_type' => $driver['driver_type'],
                    'status' => $driver['status'],
                    'is_verified' => $driver['is_verified'],
                    'profile_photo' => 'drivers/profile/driver-' . ($index + 1) . '.jpg',
                    'license_front' => 'drivers/license/front-' . ($index + 1) . '.jpg',
                    'license_back' => 'drivers/license/back-' . ($index + 1) . '.jpg',
                    'aadhaar_front' => 'drivers/aadhaar/front-' . ($index + 1) . '.jpg',
                    'aadhaar_back' => 'drivers/aadhaar/back-' . ($index + 1) . '.jpg',
                    'pan_card_file' => 'drivers/pan/pan-' . ($index + 1) . '.jpg',
                    'police_verification_file' => 'drivers/police/police-' . ($index + 1) . '.pdf',
                    'medical_certificate' => 'drivers/medical/medical-' . ($index + 1) . '.pdf',
                    'remarks' => 'Seeded driver record',
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
