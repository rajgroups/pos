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
    public function __construct(protected BookingService $bookingService) {}

    public function accept(Request $request, $booking): JsonResponse
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
            (int) $booking,
            $driver->id,
            (int) $validated['data']['vehicle_id']
        );
    }
    public function start(Request $request, $booking): JsonResponse
    {
        $validated = ValidationHelper::validate($request->all(), [
            'start_otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validated['status'] === 'error') {
            return ApiResponseHelper::error(
                $validated['message'],
                $validated['errors']
            );
        }

        $driver = $request->user();
        if (! $driver instanceof Driver) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Only drivers can start bookings.',
            ], 403);
        }

        $booking = Booking::find($booking);
        if (! $booking) {
            return response()->json([
                'status' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        if ((int) $booking->driver_id !== (int) $driver->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not assigned to this booking.',
            ], 403);
        }

        $booking = $this->bookingService->startBooking(
            $booking,
            $validated['data']['start_otp']
        );

        return response()->json([
            'status' => true,
            'message' => 'Booking started successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    public function complete(Request $request, $booking): JsonResponse
    {
        $validated = ValidationHelper::validate($request->all(), [
            'end_otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validated['status'] === 'error') {
            return ApiResponseHelper::error(
                $validated['message'],
                $validated['errors']
            );
        }

        $driver = $request->user();

        if (! $driver instanceof Driver) {
            return ApiResponseHelper::error(
                'Unauthorized. Only drivers can complete bookings.',
                null,
                403
            );
        }

        $booking = Booking::find($booking);

        if (! $booking) {
            return ApiResponseHelper::error(
                'Booking not found.',
                null,
                404
            );
        }

        if ((int) $booking->driver_id !== (int) $driver->id) {
            return ApiResponseHelper::error(
                'You are not assigned to this booking.',
                null,
                403
            );
        }

        $booking = $this->bookingService->completeBooking(
            $booking,
            $validated['data']
        );

        return response()->json([
            'status' => true,
            'message' => 'Booking completed successfully.',
            'data' => new BookingResource($booking),
        ]);
    }
}
