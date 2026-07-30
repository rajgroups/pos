<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\SosAlert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SosController extends Controller
{
    /**
     * Trigger a new SOS alert for an active booking.
     *
     * POST /api/user/bookings/{booking}/sos
     */
    public function store(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        // Ensure the booking belongs to the authenticated user
        if ((int) $booking->user_id !== (int) $user->id) {
            return ApiResponseHelper::error('Unauthorized. This booking does not belong to you.', null, 403);
        }

        // Only allow SOS on active/in-progress bookings
        $activeStatuses = [
            Booking::STATUS_ACCEPTED,
            Booking::STATUS_ARRIVED,
            Booking::STATUS_STARTED,
            Booking::STATUS_IN_PROGRESS,
        ];
        if (! in_array($booking->status, $activeStatuses, true)) {
            return ApiResponseHelper::error(
                'SOS can only be triggered for an active ride.',
                null,
                422
            );
        }

        // Validate request
        $validated = $request->validate([
            'type'      => ['required', 'in:police,ambulance,emergency_contact,safety_team'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'message'   => ['nullable', 'string', 'max:500'],
        ]);

        $sos = SosAlert::create([
            'booking_id' => $booking->id,
            'user_id'    => $user->id,
            'type'       => $validated['type'],
            'latitude'   => $validated['latitude'] ?? null,
            'longitude'  => $validated['longitude'] ?? null,
            'message'    => $validated['message'] ?? null,
            'status'     => 'active',
        ]);

        return ApiResponseHelper::success('SOS alert triggered successfully.', [
            'sos_id'     => $sos->id,
            'booking_no' => $booking->booking_no,
            'type'       => $sos->type,
            'status'     => $sos->status,
            'created_at' => $sos->created_at?->toISOString(),
        ], 201);
    }

    /**
     * List all SOS alerts for a booking.
     *
     * GET /api/user/bookings/{booking}/sos
     */
    public function index(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        // Ensure the booking belongs to the authenticated user
        if ((int) $booking->user_id !== (int) $user->id) {
            return ApiResponseHelper::error('Unauthorized. This booking does not belong to you.', null, 403);
        }

        $sosAlerts = $booking->sosAlerts()
            ->select(['id', 'type', 'status', 'latitude', 'longitude', 'message', 'resolved_at', 'created_at'])
            ->get();

        return ApiResponseHelper::success('SOS alerts fetched successfully.', $sosAlerts);
    }
}
