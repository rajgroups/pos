<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiLoginRoutesTest extends TestCase
{
    public function test_user_login_route_is_registered(): void
    {
        $response = $this->postJson('/api/user/login', [
            'email' => 'user@example.com',
            'password' => 'secret123',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'status' => true,
                'message' => 'User login route is working.',
                'data' => [
                    'login_type' => 'user',
                    'identifier' => 'user@example.com',
                ],
            ]);
    }

    public function test_driver_login_route_is_registered(): void
    {
        $response = $this->postJson('/api/driver/login', [
            'phone' => '9999999999',
            'password' => 'secret123',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'status' => true,
                'message' => 'Driver login route is working.',
                'data' => [
                    'login_type' => 'driver',
                    'identifier' => '9999999999',
                ],
            ]);
    }
}
