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

            return;
        }

        Log::info('Booking dispatched successfully', [
            'booking_id' => $booking->id,
            'booking_no' => $booking->booking_no,
        ]);
    }
}
