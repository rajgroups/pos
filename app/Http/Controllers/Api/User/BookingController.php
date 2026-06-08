<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BookingAcceptRequest;
use App\Http\Requests\Api\BookingCancelRequest;
use App\Http\Requests\Api\BookingCompleteRequest;
use App\Http\Requests\Api\BookingFareSummaryRequest;
use App\Http\Requests\Api\BookingStartRequest;
use App\Http\Requests\Api\BookingStoreRequest;
use App\Http\Resources\BookingFareResource;
use App\Http\Resources\BookingFareQuoteResource;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService)
    {
    }

    public function store(BookingStoreRequest $request): JsonResponse
    {
        $booking = $this->bookingService->createBooking($request->validated() + [
            'user_id' => $request->user()?->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Booking created successfully.',
            'data' => new BookingResource($booking),
        ], 201);
    }

    public function show(Booking $booking): JsonResponse
    {
        $booking->load([
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

        return response()->json([
            'status' => true,
            'message' => 'Booking fetched successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    public function fareSummary(BookingFareSummaryRequest $request): JsonResponse
    {
        $summary = $this->bookingService->getFareSummary($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Fare summary generated successfully.',
            'data' => new BookingFareQuoteResource($summary),
        ]);
    }

    public function accept(BookingAcceptRequest $request, Booking $booking): JsonResponse
    {
        $booking = $this->bookingService->acceptBooking(
            $booking,
            (int) $request->validated('driver_id'),
            (int) $request->validated('vehicle_id')
        );

        return response()->json([
            'status' => true,
            'message' => 'Booking accepted successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    public function start(BookingStartRequest $request, Booking $booking): JsonResponse
    {
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
        $booking = $this->bookingService->completeBooking($booking, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Booking completed successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    public function cancel(BookingCancelRequest $request, Booking $booking): JsonResponse
    {
        $booking = $this->bookingService->cancelBooking($booking);

        return response()->json([
            'status' => true,
            'message' => 'Booking cancelled successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    public function fare(Booking $booking): JsonResponse
    {
        $booking->load('fare');

        if (! $booking->fare) {
            return response()->json([
                'status' => false,
                'message' => 'Booking fare not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Booking fare fetched successfully.',
            'data' => new BookingFareResource($booking->fare),
        ]);
    }
}
