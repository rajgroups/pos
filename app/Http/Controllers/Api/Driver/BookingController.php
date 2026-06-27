<?php

namespace App\Http\Controllers\Api\Driver;

use App\Helpers\ApiResponseHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BookingCompleteRequest;
use App\Http\Requests\Api\BookingStartRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Driver;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService)
    {
    }

    public function accept(Request $request, int $bookingId): JsonResponse
    {
        $validated = ValidationHelper::ValidateAcceptBooking($request->all());

        if ($validated['status'] === 'error') {
            return ApiResponseHelper::error(
                $validated['message'],
                $validated['errors']
            );
        }

        $driver = $request->user();

        if (! $driver instanceof Driver) {
            return ApiResponseHelper::error(
                'Unauthorized.',
                null,
                403
            );
        }

        return $this->bookingService->acceptBooking(
            $bookingId,
            $driver->id,
            (int) $validated['data']['vehicle_id']
        );
    }
    public function start(BookingStartRequest $request, Booking $booking): JsonResponse
    {
        $driver = $request->user();
        if (! $driver instanceof Driver) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Only drivers can start bookings.',
            ], 403);
        }

        if ($booking->driver_id !== $driver->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not assigned to this booking.',
            ], 403);
        }

        $booking = $this->bookingService->startBooking(
            $booking,
            $request->validated('start_otp')
        );

        return response()->json([
            'status' => true,
            'message' => 'Booking started successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    public function complete(BookingCompleteRequest $request, Booking $booking): JsonResponse
    {
        $driver = $request->user();
        if (! $driver instanceof Driver) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Only drivers can complete bookings.',
            ], 403);
        }

        if ($booking->driver_id !== $driver->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not assigned to this booking.',
            ], 403);
        }

        $booking = $this->bookingService->completeBooking($booking, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Booking completed successfully.',
            'data' => new BookingResource($booking),
        ]);
    }
}
