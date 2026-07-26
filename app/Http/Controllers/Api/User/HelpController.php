<?php

namespace App\Http\Controllers\Api\User;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HelpController extends Controller
{
    /**
     * Get active FAQs.
     */
    public function faqs(Request $request): JsonResponse
    {
        $category = $request->query('category');

        $query = Faq::where('is_active', true)->orderBy('sequence', 'asc');

        if ($category && $category !== 'All') {
            $query->where('category', $category);
        }

        $faqs = $query->get();

        return ApiResponseHelper::success('FAQs fetched successfully.', $faqs);
    }

    /**
     * Get user's support tickets history.
     */
    public function tickets(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponseHelper::error('User not authenticated.', null, 401);
        }

        $tickets = SupportTicket::where('user_id', $user->id)
            ->with(['booking:id,booking_no,pickup_address,drop_address,estimated_amount,status'])
            ->latest()
            ->get();

        return ApiResponseHelper::success('Support tickets fetched successfully.', $tickets);
    }

    /**
     * Create a new support ticket / query.
     */
    public function createTicket(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return ApiResponseHelper::error('User not authenticated.', null, 401);
        }

        $validator = Validator::make($request->all(), [
            'booking_id' => 'nullable|exists:bookings,id',
            'category' => 'nullable|string|max:191',
            'subject' => 'required|string|max:191',
            'message' => 'required|string|max:2000',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::error('Validation failed.', $validator->errors()->toArray(), 422);
        }

        $ticketNo = 'TKT-' . strtoupper(Str::random(6)) . '-' . rand(100, 999);

        $ticket = SupportTicket::create([
            'ticket_no' => $ticketNo,
            'user_id' => $user->id,
            'booking_id' => $request->input('booking_id'),
            'category' => $request->input('category', 'General Query'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'priority' => $request->input('priority', 'medium'),
            'status' => 'open',
        ]);

        return ApiResponseHelper::success('Support ticket submitted successfully.', $ticket, 201);
    }
}
