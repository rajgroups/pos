<?php

namespace App\Events;

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
            'category',
            'driver',
            'vehicle',
            'pickupLocation',
            'dropLocation',
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
        return [
            'id' => $this->booking->id,
            'booking_no' => $this->booking->booking_no,
            'status' => $this->booking->status,
            'service_mode' => $this->booking->service_mode,
            'scheduled_at' => $this->booking->scheduled_at?->toIso8601String(),
            'driver_id' => $this->booking->driver_id,
            'vehicle_id' => $this->booking->vehicle_id,
            'user_id' => $this->booking->user_id,
            'vehicle_category_id' => $this->booking->vehicle_category_id,
            'estimated_amount' => $this->booking->estimated_amount,
            'final_amount' => $this->booking->final_amount,
            'updated_at' => $this->booking->updated_at?->toIso8601String(),
        ];
    }
}
