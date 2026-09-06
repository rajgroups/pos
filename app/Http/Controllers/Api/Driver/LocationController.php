<?php

namespace App\Http\Controllers\Api\Driver;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\VehicleLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Update driver location via REST API (used in Economy mode).
     */
    public function update(Request $request): JsonResponse
    {
        $driver = $request->user();

        if (! $driver instanceof Driver) {
            return ApiResponseHelper::error('Unauthorized.', null, 403);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'bearing' => ['nullable', 'numeric'],
        ]);

        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];

        try {
            // Update presence store if needed, though in economy mode it relies on DB
            $presenceStore = app(\App\Services\Socket\DriverPresenceStore::class);
            $presenceStore->updateDriverLocation($driver->id, $latitude, $longitude);

            // Persist to database vehicle_locations table
            $vehicle = Vehicle::where('driver_id', $driver->id)->where('status', 'active')->first();
            if ($vehicle) {
                VehicleLocation::create([
                    'vehicle_id' => $vehicle->id,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location_updated_at' => now(),
                ]);
            }
            
            // Broadcast to active passenger (FCM or API polling handling in frontend)
            // In Economy mode, frontend passenger app will poll active ride endpoint
            // No websocket broadcast needed here, but we can update the active booking location if required.

            return ApiResponseHelper::success('Location updated successfully.', [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update driver location via API', ['error' => $e->getMessage()]);
            return ApiResponseHelper::error('Failed to update location.', null, 500);
        }
    }
}
