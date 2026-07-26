<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponseHelper;
use App\Helpers\KeywordHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BookingCancelRequest;
use App\Http\Requests\Api\BookingFareSummaryRequest;
use App\Http\Resources\BookingFareResource;
use App\Http\Resources\BookingFareQuoteResource;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponseHelper::error('User not authenticated.', null, 401);
        }

        $filters = $request->only([
            'status',
            'search',
            'date_filter',
            'from_date',
            'to_date',
            'payment_method',
            'vehicle_category_id',
            'sort_by',
            'page',
            'per_page',
            'limit',
        ]);

        $paginator = $this->bookingService->getUserBookings($user, $filters);

        return response()->json([
            'status' => true,
            'message' => 'Bookings fetched successfully.',
            'data' => BookingResource::collection($paginator->items()),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // Log::info($request->all());
        $validated = ValidationHelper::validateBookingStore($request->all());

        if ($validated[KeywordHelper::STATUS] === KeywordHelper::ERROR) {
            return ApiResponseHelper::error(
                $validated[KeywordHelper::MESSAGE],
                $validated[KeywordHelper::ERRORS]
            );
        }

        try {
            $booking = $this->bookingService->createBooking($validated[KeywordHelper::DATA] + [
                'user_id' => $request->user()?->id,
            ]);

            return ApiResponseHelper::success(
                'Booking created successfully.',
                new BookingResource($booking),
                201
            );
        } catch (ValidationException $e) {
            return ApiResponseHelper::error($e->getMessage(), $e->errors());
        }
    }

    public function show(Booking $booking): JsonResponse
    {
        $booking->load([
            'category.pricing',
            'user',
            'locations',
            'pickupLocation',
            'dropLocation',
            'usage',
            'fare',
            'driver',
            'vehicle.location',
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

    public function cancel(BookingCancelRequest $request, $bookingId): JsonResponse
    {
        $booking = Booking::find($bookingId);

        if (! $booking) {
            return ApiResponseHelper::error('Booking not found.', null, 404);
        }

        // $this->authorize('cancel', $booking);
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

    public function activeRide(Request $request): JsonResponse
    {
        $activeBooking = $this->bookingService->userActiveBooking($request->user());

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
            'vehicle.location',
            'user',
        ]);

        return ApiResponseHelper::success('Active ride found.', new BookingResource($activeBooking));
    }

    public function retry(Booking $booking): JsonResponse
    {
        if ($booking->booking_mode !== 'instant') {
            return ApiResponseHelper::error('Only instant bookings can be retried.', null, 400);
        }

        $booking = $this->bookingService->retryBooking($booking);

        return ApiResponseHelper::success(
            'Booking request resent successfully.',
            new BookingResource($booking)
        );
    }
}
