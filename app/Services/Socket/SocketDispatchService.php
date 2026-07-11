<?php

namespace App\Services\Socket;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Services\BookingService;
use Illuminate\Support\Facades\Http;

class SocketDispatchService
{
    public function dispatchBooking(Booking $booking): void
    {
        $pickup = $booking->pickupLocation;
        $drop = $booking->dropLocation;
        if (!$pickup) {
            return;
        }

        $eligibleCategoryIds = app()
            ->make(BookingService::class)
            ->resolveDispatchCategoryIds(
                $booking->vehicle_category_id
            );

        $eligibleDriverIds = Vehicle::query()
            ->whereIn('vehicle_category_id', $eligibleCategoryIds)
            ->where('status', 'active')
            ->pluck('driver_id')
            ->unique()
            ->values()
            ->toArray();

        if (empty($eligibleDriverIds)) {
            return;
        }

        $socketUrl = rtrim(config('services.socket.url', 'http://127.0.0.1:9502'), '/');

        $response = Http::asJson()->post($socketUrl . '/send_booking', [
            'latitude' => $pickup->latitude,
            'longitude' => $pickup->longitude,
            'radius' => 5,
            'driver_ids' => $eligibleDriverIds,
            'booking' => [
                'id' => $booking->id,
                'booking_no' => $booking->booking_no,
                'service_mode' => $booking->service_mode,
                'estimated_amount' => $booking->estimated_amount,

                'pickup' => [
                    'latitude' => $pickup->latitude,
                    'longitude' => $pickup->longitude,
                    'address' => $pickup->address,
                ],

                'drop' => [
                    'latitude' => $drop?->latitude,
                    'longitude' => $drop?->longitude,
                    'address' => $drop?->address,
                ],
            ],
        ]);

        if (! $response->successful()) {
            logger()->warning('Failed to dispatch booking request to socket server.', [
                'booking_id' => $booking->id,
                'booking_no' => $booking->booking_no,
                'url' => $socketUrl . '/send_booking',
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
