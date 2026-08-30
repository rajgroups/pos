<?php

namespace App\Services;

use App\Interfaces\SmsServiceInterface;
use App\Models\SmsGatewayMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AndroidSmsGatewayService implements SmsServiceInterface
{
    /**
     * Send SMS by creating a queued job in the database.
     *
     * @param string $phoneNumber
     * @param string $message
     * @return void
     */
    public function send(string $phoneNumber, string $message): void
    {
        // Normalize phone number: remove any spaces, dashes, parentheses
        $normalizedPhone = preg_replace('/[^\d+]/', '', $phoneNumber);

        // Define expiration time (default to 5 minutes)
        $expiresAt = now()->addMinutes(config('services.sms_gateway.timeout_minutes', 5));

        // Create SMS gateway message
        $smsMessage = SmsGatewayMessage::create([
            'to' => $normalizedPhone,
            'message' => $message,
            'status' => 'pending',
            'idempotency_key' => (string) Str::uuid(),
            'attempts' => 0,
            'expires_at' => $expiresAt,
        ]);

        Log::info("SMS job created: ID={$smsMessage->id}, To={$normalizedPhone}, Expiry={$expiresAt}");
    }
}
