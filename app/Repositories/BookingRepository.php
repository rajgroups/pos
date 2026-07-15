<?php

namespace App\Repositories;

use App\Models\Booking;

class BookingRepository
{
    /**
     * The booking model instance.
     *
     * @var \App\Models\Booking
     */
    protected $model;

    /**
     * Create a new repository instance.
     *
     * @param \App\Models\Booking $model
     * @return void
     */
    public function __construct(Booking $model)
    {
        $this->model = $model;
    }

    /**
     * Find an active booking for a given user ID.
     *
     * @param int $userId
     * @return \App\Models\Booking|null
     */
    public function findActiveBookingByUserId(int $userId): ?Booking
    {
        return $this->model->where('user_id', $userId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->latest()
            ->first();
    }

    /**
     * Check if a driver has an active booking.
     *
     * @param int $driverId
     * @param int $excludeBookingId
     * @return bool
     */
    public function hasActiveDriverBooking(int $driverId, int $excludeBookingId): bool
    {
        return $this->model->where('driver_id', $driverId)
            ->where('id', '!=', $excludeBookingId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();
    }

    /**
     * Check if a vehicle has an active booking.
     *
     * @param int $vehicleId
     * @param int $excludeBookingId
     * @return bool
     */
    public function hasActiveVehicleBooking(int $vehicleId, int $excludeBookingId): bool
    {
        return $this->model->where('vehicle_id', $vehicleId)
            ->where('id', '!=', $excludeBookingId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();
    }
}
