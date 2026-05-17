<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'mobile' => '9876543210',
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
        ]);

        User::create([
            'name' => 'Test User',
            'mobile' => '9999999999',
            'email' => 'test@example.com',
            'password' => Hash::make('123456'),
        ]);
    }
}
