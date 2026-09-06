<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '1234567890',
            'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
            'status' => 'active',
        ]);
    }
}
