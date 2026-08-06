<?php

namespace App\Services;

use App\Events\BookingStatusUpdated;
use App\Helpers\ApiResponseHelper;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BookingFare;
use App\Models\BookingLocation;
use App\Models\BookingUsage;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Repositories\BookingRepository;
use App\Services\Socket\SocketDispatchService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;

class BookingService
{
    public function __construct(
        protected BookingRepository $bookingRepository,
        protected SocketDispatchService $socketDispatchService
    ) {}

    public function createBooking(array $payload): Booking
    {

        $booking = DB::transaction(function () use ($payload) {
            $category = VehicleCategory::query()
                ->with('pricing')
                ->whereKey($payload['vehicle_category_id'])
                ->firstOrFail();


            if (! $category->pricing) {
                throw ValidationException::withMessages([
                    'vehicle_category_id' => 'Pricing is not configured for the selected category.',
                ]);
            }

            $serviceMode = $this->resolveServiceMode($category, $payload);

            if ($serviceMode === 'scheduled' && empty($payload['scheduled_at'])) {
                throw ValidationException::withMessages([
                    'scheduled_at' => 'Scheduled bookings require a start time.',
                ]);
            }

            if ($serviceMode === 'instant' && ! empty($payload['scheduled_at'])) {
                throw ValidationException::withMessages([
                    'scheduled_at' => 'Instant bookings cannot be scheduled for a later time.',
                ]);
            }

            $startOtp = (string) random_int(100000, 999999);
            $fare = $this->calculateFare($category, Arr::get($payload, 'usage', []));
            $initialStatus = $serviceMode === 'scheduled'
                ? Booking::STATUS_REQUESTED
                : Booking::STATUS_PENDING;

            $booking = Booking::create([
                'booking_no' => (string) Str::ulid(),
                'user_id' => $payload['user_id'],
                'driver_id' => $payload['driver_id'] ?? null,
                'vehicle_id' => $payload['vehicle_id'] ?? null,
                'vehicle_category_id' => $category->id,
                'service_mode' => $serviceMode,
                'scheduled_at' => $payload['scheduled_at'] ?? null,
                'duration_hours' => $payload['duration_hours'] ?? null,
                'start_otp' => $startOtp,
                'status' => $initialStatus,
                'estimated_amount' => $fare['total_amount'],
                'final_amount' => 0,
                'payment_method' => $payload['payment_method'] ?? null,
                'payment_status' => 'pending',
            ]);

            $this->syncLocations($booking, Arr::get($payload, 'locations', []));
            $this->syncUsage($booking, Arr::get($payload, 'usage', []));
            $this->syncFare($booking, $fare, $category);

            return $booking->load([
                'category.pricing',
                'user',
                'locations',
                'pickupLocation',
                'dropLocation',
                'usage',
                'fare',
                'driver',
                'vehicle',
                'user',
            ]);
        });

        if ($booking->service_mode === 'instant') {
            $this->socketDispatchService->dispatchBooking($booking);
        }

        return $booking;
    }

    public function getBooking(string $bookingNo): Booking
    {
        return $this->bookingRepository->findByBookingNo($bookingNo)
            ?? throw ValidationException::withMessages([
                'booking_no' => 'Booking not found.',
            ]);
    }

    public function retryBooking(Booking $booking): Booking
    {
        if (in_array($booking->status, Booking::TERMINAL_STATUSES, true)) {
            throw ValidationException::withMessages([
                'booking_no' => 'This booking can no longer be retried.',
            ]);
        }

        $booking->update([
            'status' => Booking::STATUS_PENDING,
            'driver_id' => null,
            'vehicle_id' => null,
            'start_otp' => (string) random_int(100000, 999999),
            'accepted_at' => null,
            'started_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
        ]);

        $this->broadcastBookingUpdate($booking);

        $this->socketDispatchService->dispatchBooking($booking);

        return $booking->load([
            'category.pricing',
            'user',
            'locations',
            'pickupLocation',
            'dropLocation',
            'usage',
            'fare',
            'driver',
            'vehicle',
        ]);
    }

    public function getFareSummary(array $payload): array
    {
        $category = VehicleCategory::query()
            ->with('pricing')
            ->whereKey($payload['vehicle_category_id'])
            ->firstOrFail();

        if (! $category->pricing) {
            throw ValidationException::withMessages([
                'vehicle_category_id' => 'Pricing is not configured for the selected category.',
            ]);
        }

        return $this->calculateFare($category, Arr::get($payload, 'usage', []));
    }

    public function cancelBooking(Booking $booking): Booking
    {
        $booking = DB::transaction(function () use ($booking) {
            $booking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if (in_array($booking->status, Booking::TERMINAL_STATUSES, true)) {
                throw ValidationException::withMessages([
                    'booking_no' => 'This booking cannot be cancelled.',
                ]);
            }

            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'payment_status' => $booking->payment_status === 'paid' ? 'refunded' : $booking->payment_status,
            ]);

            return $booking->fresh()->load([
                'category.pricing',
                'user',
                'locations',
                'pickupLocation',
                'dropLocation',
                'usage',
                'fare',
                'driver',
                'vehicle',
                'user',
            ]);
        });

        $this->broadcastBookingUpdate($booking);

        return $booking;
    }

    public function acceptBooking(
        int|string $bookingId,
        int $driverId,
        int $vehicleId
    ) {

        $booking = is_numeric($bookingId)
            ? Booking::find($bookingId)
            : Booking::where('booking_no', (string) $bookingId)->first();

        if (! $booking) {
            return ApiResponseHelper::error('Booking not found.', null, 404);
        }

        $lockKey = "booking_lock:{$booking->id}";
        $lock = Redis::set($lockKey, $driverId, 'EX', 10, 'NX');

        if (! $lock) {
            return ApiResponseHelper::error(
                'This booking has already been accepted by another driver.'
            );
        }

        try {

            $booking = DB::transaction(function () use ($booking, $driverId, $vehicleId) {

                $booking = Booking::whereKey($booking->id)
                    ->lockForUpdate()
                    ->first();

                $validAcceptStates = $booking->service_mode === 'scheduled'
                    ? [Booking::STATUS_REQUESTED, Booking::STATUS_SCHEDULED]
                    : [Booking::STATUS_PENDING];

                if (! in_array($booking->status, $validAcceptStates, true)) {
                    throw new \Exception('This booking is not in a valid state.');
                }

                if ($booking->driver_id) {
                    throw new \Exception('This booking has already been assigned.');
                }

                $driver = Driver::lockForUpdate()->findOrFail($driverId);
                $vehicle = Vehicle::lockForUpdate()->findOrFail($vehicleId);

                if ($driver->status !== 'active') {
                    throw new \Exception('Driver is inactive.');
                }

                if ($vehicle->status !== 'active') {
                    throw new \Exception('Vehicle is inactive.');
                }

                if ($this->bookingRepository->hasActiveDriverBooking($driverId, $booking->id)) {
                    throw new \Exception('Driver already has an active booking.');
                }

                if ($this->bookingRepository->hasActiveVehicleBooking($vehicleId, $booking->id)) {
                    throw new \Exception('Vehicle already has an active booking.');
                }

                $booking->update([
                    'driver_id'   => $driverId,
                    'vehicle_id'  => $vehicleId,
                    'status'      => Booking::STATUS_ACCEPTED,
                    'accepted_at' => now(),
                ]);

                return $booking->fresh()->load([
                    'category.pricing',
                    'user',
                    'locations',
                    'pickupLocation',
                    'dropLocation',
                    'usage',
                    'fare',
                    'driver',
                    'vehicle',
                    'user',
                ]);
            });

            $this->broadcastBookingUpdate($booking);

            return ApiResponseHelper::success(
                'Booking accepted successfully.',
                $booking
            );
        } catch (\Throwable $e) {

            return ApiResponseHelper::error(
                $e->getMessage()
            );
        } finally {

            Redis::del($lockKey);
        }
    }
    public function arrivedAtPickup(Booking $booking): Booking
    {
        $booking = DB::transaction(function () use ($booking) {
            $booking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($booking->status !== Booking::STATUS_ACCEPTED) {
                throw ValidationException::withMessages([
                    'booking_no' => 'This booking is not in a valid state to mark as arrived.',
                ]);
            }

            $booking->update([
                'status' => Booking::STATUS_ARRIVED,
                'arrived_at' => now(),
            ]);

            return $booking->fresh()->load([
                'category.pricing',
                'user',
                'locations',
                'pickupLocation',
                'dropLocation',
                'usage',
                'fare',
                'driver',
                'vehicle',
                'user',
            ]);
        });

        $this->broadcastBookingUpdate($booking);

        return $booking;
    }

    public function startBooking(Booking $booking, string $otp): Booking
    {
        $booking = DB::transaction(function () use ($booking, $otp) {
            $booking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            $validStartStates = $booking->service_mode === 'scheduled'
                ? [Booking::STATUS_ACCEPTED, Booking::STATUS_ASSIGNED, Booking::STATUS_DISPATCHED]
                : [Booking::STATUS_ACCEPTED, Booking::STATUS_ARRIVED];

            if (! in_array($booking->status, $validStartStates, true)) {
                throw ValidationException::withMessages([
                    'booking_no' => 'This booking is not in a valid state to be started.',
                ]);
            }

            if ($booking->start_otp !== $otp) {
                throw ValidationException::withMessages([
                    'start_otp' => 'The OTP is invalid.',
                ]);
            }

            if ($booking->service_mode === 'scheduled' && $booking->scheduled_at && now()->lt($booking->scheduled_at)) {
                throw ValidationException::withMessages([
                    'scheduled_at' => 'This scheduled booking cannot be started before the scheduled time.',
                ]);
            }

            $booking->update([
                'status' => Booking::STATUS_STARTED,
                'otp_verified_at' => now(),
                'start_otp' => (string) random_int(100000, 999999), // Generate new OTP for completion
                'started_at' => now(),
            ]);

            return $booking->fresh()->load([
                'category.pricing',
                'user',
                'locations',
                'pickupLocation',
                'dropLocation',
                'usage',
                'fare',
                'driver',
                'vehicle',
                'user',
            ]);
        });

        $this->broadcastBookingUpdate($booking);

        return $booking;
    }

    public function completeBooking(Booking $booking, array $payload = []): Booking
    {
        $booking = DB::transaction(function () use ($booking, $payload) {
            $booking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($booking->status !== Booking::STATUS_STARTED) {
                throw ValidationException::withMessages([
                    'booking_no' => 'Only started bookings can be completed.',
                ]);
            }

            if (($booking->start_otp ?? null) !== ($payload['end_otp'] ?? null)) {
                throw ValidationException::withMessages([
                    'end_otp' => 'The OTP is invalid.',
                ]);
            }

            if (! empty($payload['usage'])) {
                $this->syncUsage($booking, $payload['usage']);
            }

            if (! empty($payload['final_amount'])) {
                $finalAmount = (float) $payload['final_amount'];
            } else {
                $finalAmount = (float) ($booking->fare?->total_amount ?? $booking->estimated_amount);
            }

            $booking->update([
                'status' => Booking::STATUS_COMPLETED,
                'final_amount' => $finalAmount,
                'payment_method' => $payload['payment_method'] ?? $booking->payment_method,
                'payment_status' => $payload['payment_status'] ?? 'pending',
                'completed_at' => now(),
            ]);

            return $booking->fresh()->load([
                'category.pricing',
                'user',
                'locations',
                'pickupLocation',
                'dropLocation',
                'usage',
                'fare',
                'driver',
                'vehicle',
                'user',
            ]);
        });

        $this->broadcastBookingUpdate($booking);

        return $booking;
    }

    protected function syncLocations(Booking $booking, array $locations): void
    {
        foreach ($locations as $index => $location) {
            BookingLocation::create([
                'booking_id' => $booking->id,
                'location_type' => $location['location_type'],
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'address' => $location['address'] ?? null,
                'sequence' => $location['sequence'] ?? ($index + 1),
            ]);
        }
    }

    protected function syncUsage(Booking $booking, array $usage): BookingUsage
    {
        return BookingUsage::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'distance_km' => $usage['distance_km'] ?? 0,
                'hours_used' => $usage['hours_used'] ?? 0,
                'acre_used' => $usage['acre_used'] ?? 0,
                'weight_ton' => $usage['weight_ton'] ?? 0,
            ]
        );
    }

    protected function syncFare(Booking $booking, array $fare, VehicleCategory $category): BookingFare
    {
        return BookingFare::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'pricing_type' => $category->pricing?->pricing_type ?? 'fixed',
                'base_fare' => $fare['base_fare'],
                'unit_rate' => $fare['unit_rate'],
                'usage_amount' => $fare['usage_amount'],
                'extra_charge' => $fare['extra_charge'],
                'discount' => $fare['discount'],
                'total_amount' => $fare['total_amount'],
                'snapshot' => $fare['snapshot'],
            ]
        );
    }

    protected function resolveServiceMode(VehicleCategory $category, array $payload): string
    {
        $categoryMode = $category->service_mode ?: 'instant';
        $requestedMode = $payload['booking_mode'] ?? $categoryMode;

        if (! in_array($requestedMode, ['instant', 'scheduled'], true)) {
            throw ValidationException::withMessages([
                'booking_mode' => 'The selected booking mode is not supported.',
            ]);
        }

        if ($requestedMode !== $categoryMode) {
            throw ValidationException::withMessages([
                'booking_mode' => 'The selected booking mode does not match this vehicle category.',
            ]);
        }

        return $requestedMode;
    }

    protected function broadcastBookingUpdate(Booking $booking): void
    {
        event(new BookingStatusUpdated($booking));

        $bookingPayload = (new BookingResource($booking))->resolve();

        if ($booking->status === Booking::STATUS_STARTED && ! empty($booking->start_otp)) {
            $bookingPayload['start_otp'] = $booking->start_otp;
        }

        // Use an HTTP call to the Swoole server to broadcast the update.
        $socketUrl = rtrim(config('services.socket.url', 'http://127.0.0.1:9502'), '/');

        $response = Http::asJson()->post($socketUrl . '/broadcast-booking-update', [
            'type' => 'booking_status',
            'booking' => $bookingPayload,
        ]);

        if (! $response->successful()) {
            logger()->warning('Failed to broadcast booking update to socket server.', [
                'booking_id' => $booking->id,
                'booking_no' => $booking->booking_no,
                'url' => $socketUrl . '/broadcast-booking-update',
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        // Send FCM Push Notification alongside existing socket broadcast
        try {
            $fcmService = app(\App\Services\FcmNotificationService::class);
            $status = $booking->status;

            $notificationTitles = [
                Booking::STATUS_ACCEPTED => 'Ride Accepted',
                Booking::STATUS_ARRIVED => 'Driver Arrived',
                Booking::STATUS_STARTED => 'Ride Started',
                Booking::STATUS_COMPLETED => 'Ride Completed',
                Booking::STATUS_CANCELLED => 'Ride Cancelled',
            ];

            $notificationBodies = [
                Booking::STATUS_ACCEPTED => 'Your ride request has been accepted.',
                Booking::STATUS_ARRIVED => 'The driver has arrived at the pickup location.',
                Booking::STATUS_STARTED => 'The ride has started.',
                Booking::STATUS_COMPLETED => 'The ride has been completed. Thank you!',
                Booking::STATUS_CANCELLED => 'The ride has been cancelled.',
            ];

            if (isset($notificationTitles[$status])) {
                $title = $notificationTitles[$status];
                $body = $notificationBodies[$status];
                $data = [
                    'type' => 'booking_status',
                    'status' => $status,
                    'booking_id' => (string) $booking->id,
                    'booking_no' => (string) $booking->booking_no,
                ];

                if ($booking->driver && !empty($booking->driver->fcm_token)) {
                    $fcmService->sendToToken($booking->driver->fcm_token, $title, $body, $data);
                }

                if ($booking->user && !empty($booking->user->device_token)) {
                    $fcmService->sendToToken($booking->user->device_token, $title, $body, $data);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('FCM broadcast exception: ' . $e->getMessage());
        }
    }

    protected function notifyNearbyDrivers(Booking $booking)
    {
        $this->socketDispatchService->dispatchBooking($booking);
    }

    public function resolveDispatchCategoryIds(int $vehicleCategoryId): array
    {
        $category = VehicleCategory::query()
            ->whereKey($vehicleCategoryId)
            ->with([
                'children.children',
                'parent',
            ])
            ->firstOrFail();

        $ids = $this->collectCategoryIds($category);

        if ($category->parent_id) {
            $ids[] = (int) $category->parent_id;
        }

        return array_values(array_unique($ids));
    }

    protected function collectCategoryIds(VehicleCategory $category): array
    {
        $ids = [$category->id];

        foreach ($category->children ?? [] as $child) {
            $ids = array_merge($ids, $this->collectCategoryIds($child));
        }

        return array_values(array_unique($ids));
    }

    protected function calculateFare(VehicleCategory $category, array $usage): array
    {
        $pricing = $category->pricing;
        $pricingType = $pricing?->pricing_type ?? 'fixed';

        $distance = (float) ($usage['distance_km'] ?? 0);
        $hours = (float) ($usage['hours_used'] ?? 0);
        $acres = (float) ($usage['acre_used'] ?? 0);
        $tons = (float) ($usage['weight_ton'] ?? 0);

        $usageAmount = 0.0;
        $unitRate = 0.0;
        $baseFare = (float) ($pricing?->base_fare ?? 0);
        $extraCharge = 0.0;
        $discount = 0.0;

        switch ($pricingType) {
            case 'distance':
                $usageAmount = $distance;
                $unitRate = (float) ($pricing?->per_km_rate ?? 0);
                break;
            case 'hourly':
                $usageAmount = $hours;
                $unitRate = (float) ($pricing?->per_hour_rate ?? 0);
                break;
            case 'daily':
                $usageAmount = $hours > 0 ? max(1, ceil($hours / 24)) : 1;
                $unitRate = (float) ($pricing?->per_day_rate ?? 0);
                break;
            case 'acre':
                $usageAmount = $acres;
                $unitRate = (float) ($pricing?->per_acre_rate ?? 0);
                break;
            case 'weight':
                $usageAmount = $tons;
                $unitRate = (float) ($pricing?->per_ton_rate ?? 0);
                break;
            case 'fixed':
            default:
                $usageAmount = 1;
                $unitRate = 0;
                break;
        }

        $totalAmount = $baseFare + ($usageAmount * $unitRate) + $extraCharge - $discount;
        $minimumFare = (float) ($pricing?->minimum_fare ?? 0);

        if ($minimumFare > 0) {
            $totalAmount = max($totalAmount, $minimumFare);
        }

        $totalAmount = round($totalAmount, 2);

        return [
            'pricing_type' => $pricingType,
            'base_fare' => round($baseFare, 2),
            'unit_rate' => round($unitRate, 2),
            'usage_amount' => round($usageAmount, 2),
            'extra_charge' => round($extraCharge, 2),
            'discount' => round($discount, 2),
            'total_amount' => $totalAmount,
            'snapshot' => [
                'pricing_type' => $pricingType,
                'pricing_id' => $pricing?->id,
                'category_id' => $category->id,
                'category_name' => $category->name,
                'usage' => [
                    'distance_km' => $distance,
                    'hours_used' => $hours,
                    'acre_used' => $acres,
                    'weight_ton' => $tons,
                ],
                'calculation' => [
                    'base_fare' => round($baseFare, 2),
                    'unit_rate' => round($unitRate, 2),
                    'usage_amount' => round($usageAmount, 2),
                    'extra_charge' => round($extraCharge, 2),
                    'discount' => round($discount, 2),
                    'minimum_fare' => round($minimumFare, 2),
                    'total_amount' => $totalAmount,
                ],
            ],
        ];
    }

    /**
     * @param \App\Models\User $user
     * @return \App\Models\Booking|null
     */
    public function userActiveBooking(\App\Models\User $user): ?Booking
    {
        return $this->bookingRepository->findActiveBookingByUserId($user->id);
    }

    /**
     * @param \App\Models\Driver $driver
     * @return \App\Models\Booking|null
     */
    /**
     * Get paginated bookings for a user with filters.
     *
     * @param \App\Models\User $user
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserBookings(\App\Models\User $user, array $filters = [])
    {
        return $this->bookingRepository->getUserBookings($user->id, $filters);
    }

    /**
     * @param \App\Models\Driver $driver
     * @return \App\Models\Booking|null
     */
    public function driverActiveBooking(\App\Models\Driver $driver): ?Booking
    {
        return Booking::query()
            ->where('driver_id', $driver->id)
            ->whereNotIn('status', Booking::TERMINAL_STATUSES)
            ->latest()
            ->first();
    }

    /**
     * Get paginated bookings for a driver with filters.
     *
     * @param \App\Models\Driver $driver
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDriverBookings(\App\Models\Driver $driver, array $filters = [])
    {
        return $this->bookingRepository->getDriverBookings($driver->id, $filters);
    }

    /**
     * Get summary stats for a driver.
     *
     * @param \App\Models\Driver $driver
     * @return array
     */
    public function getDriverStats(\App\Models\Driver $driver): array
    {
        return $this->bookingRepository->getDriverStats($driver->id);
    }
}
