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

        Log::info('Booking dispatch started', [
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
            ->whereNotNull('driver_id')
            ->whereHas('driver', function ($query) {
                $query->where('status', 'active')
                    ->where('is_online', 1);
            })
            ->pluck('driver_id')
            ->unique()
            ->values()
            ->toArray();

        Log::info('Eligible online drivers', [
            'driver_ids' => $eligibleDriverIds,
            'count' => count($eligibleDriverIds),
        ]);

        Log::info('Eligible drivers found', [
            'booking_id' => $booking->id,
            'driver_ids' => $eligibleDriverIds,
            'count' => count($eligibleDriverIds),
        ]);

        if (empty($eligibleDriverIds)) {
            Log::warning('Dispatch aborted: No eligible online drivers found', [
                'booking_id' => $booking->id,
                'vehicle_category_id' => $booking->vehicle_category_id,
            ]);

            $booking->update([
                'status' => Booking::STATUS_NO_DRIVER_AVAILABLE,
            ]);

            return;
        }

        // Determine dynamic driver waiting time
        $rawWaitingTime = \App\Models\AppSetting::get('driver_waiting_time');
        
        $waitingTimeMinutes = 3;
        if ($rawWaitingTime !== null && is_numeric($rawWaitingTime)) {
            $parsedTime = (int) $rawWaitingTime;
            if ($parsedTime > 0) {
                $waitingTimeMinutes = $parsedTime;
            }
        }

        $expiresAt = now()->addMinutes($waitingTimeMinutes);

        Log::info('Driver waiting time resolved', [
            'booking_id' => $booking->id,
            'waiting_time_minutes' => $waitingTimeMinutes,
        ]);

        Log::info('Booking response expires at', [
            'booking_id' => $booking->id,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);

        Log::info(sprintf(
            "Booking dispatch waiting time resolved\nbooking_id: %d\nwaiting_time_minutes: %d\nexpireset: %s",
            $booking->id,
            $waitingTimeMinutes,
            $expiresAt->toDateTimeString()
        ));

        $booking->update([
            'driver_response_expires_at' => $expiresAt,
        ]);

        // Dispatch delayed job for expiry
        \App\Jobs\ExpireDriverBookingRequest::dispatch($booking->id)->delay($expiresAt);

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

        // Send FCM Push Notification ONLY to eligible online drivers alongside WebSockets
        try {
            $targetDriverIds = $booking->driver_id ? [$booking->driver_id] : $eligibleDriverIds;
            $fcmTokens = \App\Models\Driver::query()
                ->whereIn('id', $targetDriverIds)
                ->where('status', 'active')
                ->where('is_online', 1)
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->pluck('fcm_token')
                ->toArray();

            if (!empty($fcmTokens)) {
                Log::info('FCM dispatch attempted', [
                    'booking_id' => $booking->id,
                    'driver_ids' => $targetDriverIds,
                    'token_count' => count($fcmTokens),
                ]);

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
