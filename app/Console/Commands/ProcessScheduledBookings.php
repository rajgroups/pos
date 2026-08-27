<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\FcmNotificationService;
use App\Services\Socket\SocketDispatchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessScheduledBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due scheduled bookings, notify riders, and initiate driver matching.';

    /**
     * Execute the console command.
     */
    public function handle(
        SocketDispatchService $socketDispatchService,
        FcmNotificationService $fcmNotificationService
    ): int {
        $this->info('Scheduled booking processor started.');

        $dueBookings = Booking::query()
            ->where('service_mode', 'scheduled')
            ->whereIn('status', [Booking::STATUS_SCHEDULED, Booking::STATUS_REQUESTED])
            ->where('scheduled_at', '<=', now())
            ->whereNull('scheduled_processed_at')
            ->get();

        if ($dueBookings->isEmpty()) {
            $this->info('No due scheduled bookings found.');
            return Command::SUCCESS;
        }

        $this->info(sprintf('Found %d due scheduled booking(s) to process.', $dueBookings->count()));

        $processedCount = 0;
        $failedCount = 0;

        foreach ($dueBookings as $dueBooking) {
            try {
                $booking = DB::transaction(function () use ($dueBooking) {
                    $b = Booking::query()
                        ->whereKey($dueBooking->id)
                        ->lockForUpdate()
                        ->first();

                    if (! $b || $b->scheduled_processed_at !== null || ! in_array($b->status, [Booking::STATUS_SCHEDULED, Booking::STATUS_REQUESTED], true)) {
                        return null;
                    }

                    $b->update([
                        'status' => Booking::STATUS_PENDING,
                        'scheduled_processed_at' => now(),
                        'driver_search_started_at' => now(),
                        'scheduled_notification_sent_at' => now(),
                    ]);

                    return $b->fresh([
                        'category.pricing',
                        'user',
                        'locations',
                        'pickupLocation',
                        'dropLocation',
                        'usage',
                        'fare',
                    ]);
                });

                if (! $booking) {
                    Log::info('Scheduled booking skipped (already claimed or updated by another process)', [
                        'booking_id' => $dueBooking->id,
                        'booking_no' => $dueBooking->booking_no,
                    ]);
                    continue;
                }

                Log::info('Scheduled booking claimed successfully', [
                    'booking_id' => $booking->id,
                    'booking_no' => $booking->booking_no,
                    'scheduled_at' => $booking->scheduled_at?->toDateTimeString(),
                ]);

                // 1. Send FCM Push Notification to User
                if ($booking->user && ! empty($booking->user->device_token)) {
                    try {
                        $fcmNotificationService->sendToToken(
                            $booking->user->device_token,
                            'Your ride is ready',
                            'Your scheduled ride is now being arranged. Tap to find a driver.',
                            [
                                'type' => 'scheduled_ride_ready',
                                'action' => 'open_finding_driver',
                                'booking_id' => (string) $booking->id,
                                'booking_no' => (string) $booking->booking_no,
                                'status' => (string) $booking->status,
                            ]
                        );
                        Log::info('User FCM notification sent for scheduled ride', [
                            'booking_id' => $booking->id,
                            'user_id' => $booking->user_id,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Failed to send User FCM for scheduled ride', [
                            'booking_id' => $booking->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                } else {
                    Log::warning('User device_token missing for scheduled ride notification', [
                        'booking_id' => $booking->id,
                        'user_id' => $booking->user_id,
                    ]);
                }

                // 2. Trigger Driver Matching
                try {
                    $socketDispatchService->dispatchBooking($booking);
                    Log::info('Driver search started for scheduled booking', [
                        'booking_id' => $booking->id,
                        'booking_no' => $booking->booking_no,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Error triggering driver dispatch for scheduled booking', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                $processedCount++;
                $this->info(sprintf('Successfully processed scheduled booking #%s', $booking->booking_no));
            } catch (\Throwable $e) {
                $failedCount++;
                Log::error('Exception processing scheduled booking', [
                    'booking_id' => $dueBooking->id,
                    'booking_no' => $dueBooking->booking_no,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $this->error(sprintf('Failed to process scheduled booking #%s: %s', $dueBooking->booking_no, $e->getMessage()));
            }
        }

        $this->info(sprintf('Scheduled processing complete. Processed: %d, Failed: %d', $processedCount, $failedCount));

        return Command::SUCCESS;
    }
}
