<?php

namespace Tests\Feature;

use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverFcmTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_otp_verification_updates_fcm_token(): void
    {
        $driver = Driver::create([
            'name' => 'Test Driver',
            'phone' => '9876543210',
            'otp' => '1234',
            'license_number' => 'DL-9876543210',
        ]);

        $response = $this->postJson('/api/driver/verify-otp', [
            'mobile' => '9876543210',
            'otp' => '1234',
            'fcm_token' => 'sample_fcm_token_123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'driver' => [
                        'id' => $driver->id,
                        'fcm_token' => 'sample_fcm_token_123',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'fcm_token' => 'sample_fcm_token_123',
        ]);
    }

    public function test_authenticated_driver_can_update_fcm_token(): void
    {
        $driver = Driver::create([
            'name' => 'Test Driver',
            'phone' => '9876543211',
            'otp' => null,
            'license_number' => 'DL-9876543211',
        ]);

        $token = $driver->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/driver/fcm-token', [
                'fcm_token' => 'updated_fcm_token_456',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'fcm_token' => 'updated_fcm_token_456',
                ],
            ]);

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'fcm_token' => 'updated_fcm_token_456',
        ]);
    }
}
