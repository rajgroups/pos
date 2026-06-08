<?php

namespace App\Services;

use App\Events\BookingStatusUpdated;
use App\Models\Booking;
use App\Models\BookingFare;
use App\Models\BookingLocation;
use App\Models\BookingUsage;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Repositories\BookingRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(protected BookingRepository $bookingRepository)
    {
    }

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
            $initialStatus = $serviceMode === 'scheduled' ? 'requested' : 'pending';

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

    public function getBooking(string $bookingNo): Booking
    {
        return $this->bookingRepository->findByBookingNo($bookingNo)
            ?? throw ValidationException::withMessages([
                'booking_no' => 'Booking not found.',
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

            if (in_array($booking->status, ['completed', 'cancelled'], true)) {
                throw ValidationException::withMessages([
                    'booking_no' => 'This booking cannot be cancelled.',
                ]);
            }

            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'payment_status' => $booking->payment_status === 'paid' ? 'refunded' : $booking->payment_status,
            ]);

            return $booking->fresh()->load([
                'category.pricing',
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

    public function acceptBooking(Booking $booking, int $driverId, int $vehicleId): Booking
    {
        $booking = DB::transaction(function () use ($booking, $driverId, $vehicleId) {
            $booking = Booking::query()->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            $validAcceptStates = $booking->service_mode === 'scheduled' ? ['requested', 'scheduled'] : ['pending'];

            if (! in_array($booking->status, $validAcceptStates, true)) {
                throw ValidationException::withMessages([
                    'booking_no' => 'This booking is not in a valid state to be accepted.',
                ]);
            }

            $driver = Driver::query()->whereKey($driverId)->lockForUpdate()->firstOrFail();
            $vehicle = Vehicle::query()->whereKey($vehicleId)->lockForUpdate()->firstOrFail();

            if ($driver->status !== 'active') {
                throw ValidationException::withMessages([
                    'driver_id' => 'This driver is not active.',
                ]);
            }

            if ($vehicle->status !== 'active') {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'This vehicle is not active.',
                ]);
            }

            if ($this->bookingRepository->hasActiveDriverBooking($driverId, $booking->id)) {
                throw ValidationException::withMessages([
                    'driver_id' => 'This driver already has an active booking.',
                ]);
            }

            if ($this->bookingRepository->hasActiveVehicleBooking($vehicleId, $booking->id)) {
                throw ValidationException::withMessages([
                    'vehicle_id' => 'This vehicle already has an active booking.',
                ]);
            }

            $booking->update([
                'driver_id' => $driverId,
                'vehicle_id' => $vehicleId,
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            return $booking->fresh()->load([
                'category.pricing',
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
                ? ['accepted', 'assigned', 'dispatched']
                : ['accepted', 'arrived'];

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
                'status' => 'started',
                'otp_verified_at' => now(),
                'started_at' => now(),
            ]);

            return $booking->fresh()->load([
                'category.pricing',
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

            if ($booking->status !== 'started') {
                throw ValidationException::withMessages([
                    'booking_no' => 'Only started bookings can be completed.',
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
                'status' => 'completed',
                'final_amount' => $finalAmount,
                'payment_method' => $payload['payment_method'] ?? $booking->payment_method,
                'payment_status' => $payload['payment_status'] ?? 'pending',
                'completed_at' => now(),
            ]);

            return $booking->fresh()->load([
                'category.pricing',
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
}
