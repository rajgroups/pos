<?php

namespace App\Services\Socket;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\BookingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocketDispatchService
{

    public function dispatchBooking(Booking $booking): void
    {
        $booking->loadMissing([
            'category.pricing',
            'user',
            'driver',
            'vehicle',
            'locations',
            'pickupLocation',
            'dropLocation',
            'usage',
            'fare',
        ]);

        Log::info('Dispatch booking started', [
            'booking_id' => $booking->id,
            'booking_no' => $booking->booking_no,
        ]);

        $pickup = $booking->pickupLocation;
        $drop = $booking->dropLocation;

        if (! $pickup) {
            Log::warning('Dispatch aborted: Pickup location not found', [
                'booking_id' => $booking->id,
            ]);

            return;
        }

        Log::info('Pickup location loaded', [
            'latitude' => $pickup->latitude,
            'longitude' => $pickup->longitude,
        ]);

        $eligibleCategoryIds = app()
            ->make(BookingService::class)
            ->resolveDispatchCategoryIds(
                $booking->vehicle_category_id
            );

        Log::info('Eligible vehicle categories', [
            'category_ids' => $eligibleCategoryIds,
        ]);

        $eligibleDriverIds = Vehicle::query()
            ->whereIn('vehicle_category_id', $eligibleCategoryIds)
            ->where('status', 'active')
            ->pluck('driver_id')
            ->unique()
            ->values()
            ->toArray();

        Log::info('Eligible drivers', [
            'driver_ids' => $eligibleDriverIds,
            'count' => count($eligibleDriverIds),
        ]);

        if (empty($eligibleDriverIds)) {
            Log::warning('Dispatch aborted: No eligible drivers found', [
                'booking_id' => $booking->id,
            ]);

            return;
        }

        $socketUrl = rtrim(config('services.socket.url', 'http://127.0.0.1:9502'), '/');

        Log::info('Sending booking to socket server', [
            'url' => $socketUrl . '/send_booking',
        ]);

        $payload = [
            'latitude' => $pickup->latitude,
            'longitude' => $pickup->longitude,
            'radius' => 5,
            'driver_ids' => $eligibleDriverIds,
            'booking' => (new BookingResource($booking))->resolve(),
        ];

        Log::info('Socket request payload', $payload);

        $response = Http::asJson()->post(
            $socketUrl . '/send_booking',
            $payload
        );

        Log::info('Socket server response', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $response->body(),
        ]);

        if (! $response->successful()) {
            Log::warning('Failed to dispatch booking request to socket server.', [
                'booking_id' => $booking->id,
                'booking_no' => $booking->booking_no,
                'url' => $socketUrl . '/send_booking',
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } else {
            Log::info('Booking dispatched successfully to socket server', [
                'booking_id' => $booking->id,
                'booking_no' => $booking->booking_no,
            ]);
        }

        // Send FCM Push Notification to eligible drivers alongside WebSockets
        try {
            $targetDriverIds = $booking->driver_id ? [$booking->driver_id] : $eligibleDriverIds;
            $fcmTokens = \App\Models\Driver::query()
                ->whereIn('id', $targetDriverIds)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')
                ->toArray();

            if (!empty($fcmTokens)) {
                $fcmService = app(\App\Services\FcmNotificationService::class);
                $title = 'New Ride Request';
                $body = 'Tap to view and accept the ride request from ' . ($booking->user?->name ?? 'a rider');
                $data = [
                    'type' => 'new_ride_request',
                    'booking_id' => (string) $booking->id,
                    'booking_no' => (string) $booking->booking_no,
                    'status' => (string) ($booking->status ?? Booking::STATUS_PENDING),
                    'driver_id' => (string) ($booking->driver_id ?? ''),
                    'vehicle_id' => (string) ($booking->vehicle_id ?? ''),
                    'vehicle_category_id' => (string) ($booking->vehicle_category_id ?? ''),
                    'scheduled_at' => (string) ($booking->scheduled_at ?? ''),
                    'passenger_name' => (string) ($booking->user?->name ?? ''),
                    'pickup_address' => (string) ($booking->pickupLocation?->address ?? ''),
                    'drop_address' => (string) ($booking->dropLocation?->address ?? ''),
                    'estimated_amount' => (string) ($booking->estimated_amount ?? 0),
                    'booking_mode' => (string) ($booking->service_mode ?? ''),
                    'vehicle_name' => (string) ($booking->vehicle?->model ?? ''),
                    'vehicle_number' => (string) ($booking->vehicle?->vehicle_number ?? ''),
                    'notes' => (string) ($booking->notes ?? ''),
                ];
                $fcmService->sendToTokens($fcmTokens, $title, $body, $data);
            }
        } catch (\Throwable $e) {
            Log::error('FCM dispatch error in SocketDispatchService: ' . $e->getMessage());
        }
    }
}
