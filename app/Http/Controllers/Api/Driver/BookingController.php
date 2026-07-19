<?php

namespace App\Http\Controllers\Api\Driver;

use App\Helpers\ApiResponseHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BookingCompleteRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
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

        $vehicleId = $validated['data']['vehicle_id'] ?? null;

        if ($vehicleId === null) {
            $vehicleId = $driver->vehicleAssignments()
                ->where('is_current', true)
                ->orderByDesc('assigned_from')
                ->value('vehicle_id');
        }

        if ($vehicleId === null) {
            $vehicleId = Vehicle::query()
                ->where('driver_id', $driver->id)
                ->where('status', 'active')
                ->orderByDesc('id')
                ->value('id');
        }

        if ($vehicleId === null) {
            return ApiResponseHelper::error(
                'Vehicle ID is required.',
                null,
                422
            );
        }

        return $this->bookingService->acceptBooking(
            (int) $booking,
            $driver->id,
            (int) $vehicleId
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

    public function complete(BookingCompleteRequest $request, $booking): JsonResponse
    {
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
            $request->validated()
        );

        return response()->json([
            'status' => true,
            'message' => 'Booking completed successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

  public function show(int $bookingId): JsonResponse
    {
        $booking = Booking::with([
            'category.pricing',
            'user',
            'locations',
            'pickupLocation',
            'dropLocation',
            'usage',
            'fare',
            'driver',
            'vehicle',
        ])->findOrFail($bookingId);

        return response()->json([
            'status' => true,
            'message' => 'Booking fetched successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    public function activeRide(Request $request): JsonResponse
    {
        $activeBooking = $this->bookingService->driverActiveBooking($request->user());

        if (! $activeBooking) {
            return ApiResponseHelper::success(
                'No active ride found.',
                null
            );
        }

        $activeBooking->load([
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

        return ApiResponseHelper::success('Active ride found.', new BookingResource($activeBooking));
    }
}
