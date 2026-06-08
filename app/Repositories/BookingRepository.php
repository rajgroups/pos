<?php

namespace App\Repositories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository
{
    public function queryWithRelations(): Builder
    {
        return Booking::query()->with([
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
    }

    public function findByBookingNo(string $bookingNo): ?Booking
    {
        return $this->queryWithRelations()
            ->where('booking_no', $bookingNo)
            ->first();
    }

    public function lockByBookingNo(string $bookingNo): ?Booking
    {
        return Booking::query()
            ->where('booking_no', $bookingNo)
            ->lockForUpdate()
            ->first();
    }

    public function create(array $attributes): Booking
    {
        return Booking::create($attributes);
    }

    public function getActiveStatuses(): array
    {
        return ['accepted', 'started'];
    }

    public function hasActiveDriverBooking(int $driverId, ?int $ignoreBookingId = null): bool
    {
        return Booking::query()
            ->where('driver_id', $driverId)
            ->whereIn('status', $this->getActiveStatuses())
            ->when($ignoreBookingId, fn ($query) => $query->where('id', '!=', $ignoreBookingId))
            ->exists();
    }

    public function hasActiveVehicleBooking(int $vehicleId, ?int $ignoreBookingId = null): bool
    {
        return Booking::query()
            ->where('vehicle_id', $vehicleId)
            ->whereIn('status', $this->getActiveStatuses())
            ->when($ignoreBookingId, fn ($query) => $query->where('id', '!=', $ignoreBookingId))
            ->exists();
    }

    public function forUser(int $userId): Collection
    {
        return $this->queryWithRelations()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}
