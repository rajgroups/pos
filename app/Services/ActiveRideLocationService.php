<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ActiveRideLocationService
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Create the active ride node in Firebase RTDB after driver assignment
     */
    public function createActiveRide($booking): bool
    {
        try {
            $data = [
                'booking_id' => $booking->id,
                'booking_no' => $booking->booking_no,
                'driver_id' => $booking->driver_id,
                'user_id' => $booking->user_id,
                'ride_status' => $booking->status,
                'updated_at' => time(),
                'last_seen' => time(),
            ];

            // If the driver has a known location (e.g., from driver:location Redis or latest VehicleLocation)
            // we can populate latitude and longitude here as an initial state.
            
            return $this->firebaseService->setActiveRide($booking->id, $data);
        } catch (\Exception $e) {
            Log::error('ActiveRideLocationService::createActiveRide error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the active ride status
     */
    public function updateRideStatus($booking, string $status): bool
    {
        try {
            $data = [
                'ride_status' => $status,
                'updated_at' => time(),
            ];
            
            return $this->firebaseService->updateActiveRide($booking->id, $data);
        } catch (\Exception $e) {
            Log::error('ActiveRideLocationService::updateRideStatus error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove the active ride node in Firebase RTDB
     */
    public function cleanupActiveRide($booking): bool
    {
        try {
            return $this->firebaseService->deleteActiveRide($booking->id);
        } catch (\Exception $e) {
            Log::error('ActiveRideLocationService::cleanupActiveRide error: ' . $e->getMessage());
            return false;
        }
    }
}
