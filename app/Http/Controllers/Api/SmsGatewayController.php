<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SmsGatewayDevice;
use App\Models\SmsGatewayMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SmsGatewayController extends Controller
{
    /**
     * Register a new or existing gateway device.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'device_identifier' => 'required|string',
            'setup_token' => 'required|string',
            'name' => 'nullable|string',
            'phone_number' => 'nullable|string',
        ]);

        $configuredToken = config('services.sms_gateway.registration_token') ?? env('SMS_GATEWAY_REGISTRATION_TOKEN');

        if (!$configuredToken || $request->input('setup_token') !== $configuredToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid setup registration token.'
            ], 403);
        }

        $token = Str::random(60);
        $tokenHash = hash('sha256', $token);

        $device = SmsGatewayDevice::updateOrCreate(
            ['device_identifier' => $request->input('device_identifier')],
            [
                'name' => $request->input('name') ?? 'Android Device',
                'phone_number' => $request->input('phone_number'),
                'status' => 'active',
                'token_hash' => $tokenHash,
                'last_seen_at' => now(),
            ]
        );

        Log::info("SMS Gateway Device registered/updated: ID={$device->id}, Name={$device->name}");

        return response()->json([
            'success' => true,
            'token' => $token,
            'device' => [
                'id' => $device->id,
                'name' => $device->name,
                'device_identifier' => $device->device_identifier,
                'phone_number' => $device->phone_number,
                'status' => $device->status,
            ]
        ]);
    }

    /**
     * Send heartbeat to keep the gateway status alive.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $device = $request->get('sms_gateway_device');
        
        $device->update([
            'last_seen_at' => now(),
            'phone_number' => $request->input('phone_number') ?? $device->phone_number,
        ]);

        return response()->json([
            'success' => true,
            'status' => $device->status,
        ]);
    }

    /**
     * Poll for pending SMS jobs.
     */
    public function jobs(Request $request): JsonResponse
    {
        $device = $request->get('sms_gateway_device');

        // Mark old pending messages as expired
        SmsGatewayMessage::where('status', 'pending')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        // Fetch pending messages
        $messages = SmsGatewayMessage::where('status', 'pending')
            ->where('expires_at', '>', now())
            ->lockForUpdate() // Avoid race conditions between multiple gateways
            ->get();

        $claimedJobs = [];

        foreach ($messages as $message) {
            $message->update([
                'status' => 'processing',
                'gateway_device_id' => $device->id,
                'attempts' => $message->attempts + 1,
            ]);

            $claimedJobs[] = [
                'id' => $message->id,
                'to' => $message->to,
                'message' => $message->message,
                'idempotency_key' => $message->idempotency_key,
            ];
        }

        return response()->json([
            'success' => true,
            'jobs' => $claimedJobs,
        ]);
    }

    /**
     * Update the sending result of a claimed SMS job.
     */
    public function reportResult(Request $request, int $id): JsonResponse
    {
        $device = $request->get('sms_gateway_device');
        $request->validate([
            'status' => 'required|string|in:sent,failed',
            'error' => 'nullable|string',
        ]);

        $message = SmsGatewayMessage::where('id', $id)
            ->where('gateway_device_id', $device->id)
            ->first();

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'SMS job not found or not claimed by this device.'
            ], 404);
        }

        if ($request->input('status') === 'sent') {
            $message->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            Log::info("SMS job {$id} sent successfully.");
        } else {
            $message->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => $request->input('error'),
            ]);
            Log::warning("SMS job {$id} failed to send: " . $request->input('error'));
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
