<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Submit or update a review for a booking.
     */
    public function store(Request $request, $bookingId): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponseHelper::error('User not authenticated.', null, 401);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'feedback_tags' => ['nullable', 'array'],
            'feedback_tags.*' => ['string', 'max:100'],
        ]);

        $booking = is_numeric($bookingId)
            ? Booking::find($bookingId)
            : Booking::where('booking_no', (string) $bookingId)->first();

        if (! $booking) {
            return ApiResponseHelper::error('Booking not found.', null, 404);
        }

        if ((int) $booking->user_id !== (int) $user->id) {
            return ApiResponseHelper::error('Unauthorized. This booking does not belong to you.', null, 403);
        }

        if ($booking->status !== Booking::STATUS_COMPLETED) {
            return ApiResponseHelper::error('Only completed rides can be reviewed.', null, 422);
        }

        $review = DB::transaction(function () use ($booking, $user, $validated) {
            return Review::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'reviewed_by' => 'user',
                ],
                [
                    'user_id' => $user->id,
                    'driver_id' => $booking->driver_id,
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment'] ?? null,
                    'feedback_tags' => $validated['feedback_tags'] ?? [],
                ]
            );
        });

        return ApiResponseHelper::success(
            'Review submitted successfully.',
            new ReviewResource($review)
        );
    }

    /**
     * Get review details for a booking.
     */
    public function show(Request $request, $bookingId): JsonResponse
    {
        $user = $request->user();

        $booking = is_numeric($bookingId)
            ? Booking::find($bookingId)
            : Booking::where('booking_no', (string) $bookingId)->first();

        if (! $booking) {
            return ApiResponseHelper::error('Booking not found.', null, 404);
        }

        $review = Review::where('booking_id', $booking->id)
            ->where('reviewed_by', 'user')
            ->first();

        if (! $review) {
            return ApiResponseHelper::error('Review not found for this booking.', null, 404);
        }

        return ApiResponseHelper::success(
            'Review fetched successfully.',
            new ReviewResource($review)
        );
    }
}
