<?php

namespace App\Events;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingRequested implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Booking $booking,
        public int $driverId
    ) {
        $this->booking->loadMissing([
            'category',
            'user',
            'pickupLocation',
            'dropLocation',
        ]);
    }

    public function broadcastOn(): array
    {
        // Broadcast to a driver-specific private channel.
        // e.g., driver app listens to PrivateChannel('drivers.123')
        return [
            new PrivateChannel('drivers.' . $this->driverId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'booking_request';
    }

    public function broadcastWith(): array
    {
        return [
            'booking' => (new BookingResource($this->booking))->resolve(),
        ];
    }
}
