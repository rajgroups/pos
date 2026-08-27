<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLoginRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_route_is_registered(): void
    {
        $user = User::factory()->create([
            'mobile' => '9876543210',
        ]);

        $response = $this->postJson('/api/user/send-otp', [
            'mobile' => '9876543210',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success');
    }

    public function test_driver_login_route_is_registered(): void
    {
        $driver = Driver::create([
            'name' => 'Test Driver OTP',
            'phone' => '9999999999',
            'email' => 'driver_otp@example.com',
            'city' => 'Chennai',
            'state' => 'Tamil Nadu',
            'driver_type' => 'car',
            'license_categories' => ['LMV'],
            'license_number' => 'DL-99999999999',
            'status' => 'active',
            'is_verified' => true,
        ]);

        $response = $this->postJson('/api/driver/send-otp', [
            'mobile' => '9999999999',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success');
    }
}
