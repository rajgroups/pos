<?php

namespace Tests\Feature;

use App\Models\SmsGatewayDevice;
use App\Models\SmsGatewayMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set a config value for setup token
        config(['services.sms_gateway.registration_token' => 'test-setup-token']);
    }

    public function test_device_registration_with_valid_token(): void
    {
        $response = $this->postJson('/api/v1/sms-gateway/register', [
            'device_identifier' => 'test-device-uuid',
            'setup_token' => 'test-setup-token',
            'name' => 'My Test Android Phone',
            'phone_number' => '+919876543210',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'token', 'device']);

        $this->assertDatabaseHas('sms_gateway_devices', [
            'device_identifier' => 'test-device-uuid',
            'phone_number' => '+919876543210',
            'status' => 'active',
        ]);
    }

    public function test_device_registration_with_invalid_token(): void
    {
        $response = $this->postJson('/api/v1/sms-gateway/register', [
            'device_identifier' => 'test-device-uuid',
            'setup_token' => 'wrong-setup-token',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_heartbeat_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/sms-gateway/heartbeat');
        $response->assertStatus(401);
    }

    public function test_heartbeat_updates_last_seen(): void
    {
        $token = 'random-bearer-token-string';
        $tokenHash = hash('sha256', $token);

        $device = SmsGatewayDevice::create([
            'device_identifier' => 'test-device-uuid',
            'name' => 'Test Phone',
            'token_hash' => $tokenHash,
            'status' => 'active',
            'last_seen_at' => now()->subHours(2),
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/sms-gateway/heartbeat', [
                'phone_number' => '+919999999999',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $device->refresh();
        $this->assertEquals('+919999999999', $device->phone_number);
        $this->assertTrue($device->last_seen_at->gt(now()->subMinute()));
    }

    public function test_sms_jobs_flow_user_otp(): void
    {
        // 1. Create a user
        $user = User::factory()->create([
            'mobile' => '+919876543210',
        ]);

        // Disable returning OTP in API response to test gateway behavior
        config(['services.sms_gateway.debug_return_otp' => false]);

        // 2. Request OTP (should create a job in sms_gateway_messages)
        $otpResponse = $this->postJson('/api/user/send-otp', [
            'mobile' => '+919876543210',
        ]);

        $otpResponse->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonMissingPath('data.otp'); // OTP should not be returned in API response

        $this->assertDatabaseHas('sms_gateway_messages', [
            'to' => '+919876543210',
            'status' => 'pending',
        ]);

        $smsJob = SmsGatewayMessage::where('to', '+919876543210')->first();
        $this->assertNotNull($smsJob);

        // 3. Register gateway device
        $token = 'gateway-secret-token';
        $tokenHash = hash('sha256', $token);
        $device = SmsGatewayDevice::create([
            'device_identifier' => 'device-uuid',
            'name' => 'Gateway Phone',
            'token_hash' => $tokenHash,
            'status' => 'active',
        ]);

        // 4. Poll jobs
        $jobsResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/sms-gateway/jobs');

        $jobsResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'jobs')
            ->assertJsonPath('jobs.0.id', $smsJob->id)
            ->assertJsonPath('jobs.0.to', '+919876543210');

        $smsJob->refresh();
        $this->assertEquals('processing', $smsJob->status);
        $this->assertEquals($device->id, $smsJob->gateway_device_id);

        // 5. Report success result
        $resultResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson("/api/v1/sms-gateway/jobs/{$smsJob->id}/result", [
                'status' => 'sent',
            ]);

        $resultResponse->assertOk()
            ->assertJsonPath('success', true);

        $smsJob->refresh();
        $this->assertEquals('sent', $smsJob->status);
        $this->assertNotNull($smsJob->sent_at);
    }
}
