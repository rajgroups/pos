<?php

namespace App\Events;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->loadMissing([
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
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('bookings.' . $this->booking->booking_no),
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking.status.updated';
    }

    public function broadcastWith(): array
    {
        $booking = (new BookingResource($this->booking))->resolve();

        if ($this->booking->status === Booking::STATUS_STARTED && ! empty($this->booking->start_otp)) {
            $booking['start_otp'] = $this->booking->start_otp;
        }

        return [
            'booking' => $booking,
        ];
    }
}
