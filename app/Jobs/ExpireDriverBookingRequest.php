<?php

namespace App\Jobs;

use App\Events\BookingStatusUpdated;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ExpireDriverBookingRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $bookingId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(BookingService $bookingService): void
    {
        Log::info('Booking expiry job started', [
            'booking_id' => $this->bookingId,
        ]);

        $booking = Booking::find($this->bookingId);

        if (!$booking) {
            Log::warning('Booking not found in expiry job', [
                'booking_id' => $this->bookingId,
            ]);
            return;
        }

        // Perform atomic check and update inside a transaction to prevent race conditions
        $updated = DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::whereKey($this->bookingId)
                ->lockForUpdate()
                ->first();

            if (!$lockedBooking) {
                return false;
            }

            if ($lockedBooking->driver_response_expires_at && $lockedBooking->driver_response_expires_at->isFuture()) {
                Log::info('Booking response time has not expired yet - skip expiry', [
                    'booking_id' => $this->bookingId,
                    'expires_at' => $lockedBooking->driver_response_expires_at->toDateTimeString(),
                ]);
                return false;
            }

            if ($lockedBooking->status === Booking::STATUS_CANCELLED) {
                Log::info('Booking already cancelled - skip expiry', [
                    'booking_id' => $this->bookingId,
                ]);
                return false;
            }

            if ($lockedBooking->driver_id !== null || $lockedBooking->accepted_at !== null || in_array($lockedBooking->status, [Booking::STATUS_ACCEPTED, Booking::STATUS_STARTED, Booking::STATUS_COMPLETED], true)) {
                Log::info('Booking already accepted - skip expiry', [
                    'booking_id' => $this->bookingId,
                ]);
                return false;
            }

            if (!in_array($lockedBooking->status, [Booking::STATUS_PENDING, Booking::STATUS_REQUESTED, Booking::STATUS_SEARCHING_DRIVER], true)) {
                Log::info('Booking in terminal or non-pending state - skip expiry', [
                    'booking_id' => $this->bookingId,
                    'status' => $lockedBooking->status,
                ]);
                return false;
            }

            $lockedBooking->update([
                'status' => Booking::STATUS_NO_DRIVER_AVAILABLE,
            ]);

            return true;
        });

        if ($updated) {
            Log::info('Booking expired - no driver accepted', [
                'booking_id' => $this->bookingId,
            ]);

            $freshBooking = Booking::find($this->bookingId);
            $freshBooking->loadMissing([
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

            // Broadcast update via BookingService broadcastBookingUpdate
            try {
                $bookingService->broadcastBookingUpdate($freshBooking);
            } catch (\Throwable $e) {
                Log::error('Failed to broadcast expiry status update to socket server', [
                    'booking_id' => $this->bookingId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
