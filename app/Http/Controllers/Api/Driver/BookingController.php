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
use Illuminate\Support\Facades\DB;


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
    public function arrived(Request $request, $booking): JsonResponse
    {
        $driver = $request->user();

        if (! $driver instanceof Driver) {
            return ApiResponseHelper::error(
                'Unauthorized. Only drivers can update booking status.',
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

        $booking = $this->bookingService->arrivedAtPickup($booking);

        return response()->json([
            'status' => true,
            'message' => 'Arrived at pickup location.',
            'data' => new BookingResource($booking),
        ]);
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

    public function index(Request $request): JsonResponse
    {
        $driver = $request->user();

        if (! $driver instanceof Driver) {
            return ApiResponseHelper::error('Unauthorized.', null, 401);
        }

        $filters = $request->only([
            'status',
            'search',
            'date_filter',
            'from_date',
            'to_date',
            'payment_method',
            'vehicle_category_id',
            'type',
            'min_price',
            'max_price',
            'sort_by',
            'page',
            'per_page',
            'limit',
        ]);

        $paginator = $this->bookingService->getDriverBookings($driver, $filters);
        $stats = $this->bookingService->getDriverStats($driver);

        return response()->json([
            'status' => true,
            'message' => 'Driver bookings fetched successfully.',
            'data' => BookingResource::collection($paginator->items()),
            'meta' => array_merge([
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ], $stats),
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

    public function fare($bookingId): JsonResponse
    {
        $booking = Booking::with(['fare', 'category.pricing', 'user', 'driver', 'vehicle', 'pickupLocation', 'dropLocation'])->find($bookingId);

        if (! $booking) {
            return response()->json([
                'status' => false,
                'message' => 'Booking not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Booking fare fetched successfully.',
            'data' => [
                'booking_id' => $booking->booking_no,
                'status' => $booking->status,
                'base_fare' => (float) ($booking->fare?->base_fare ?? 0),
                'usage_amount' => (float) ($booking->fare?->usage_amount ?? 0),
                'extra_charge' => (float) ($booking->fare?->extra_charge ?? 0),
                'discount' => (float) ($booking->fare?->discount ?? 0),
                'total_amount' => (float) ($booking->final_amount > 0 ? $booking->final_amount : ($booking->fare?->total_amount ?? $booking->estimated_amount)),
                'payment_method' => $booking->payment_method ?? 'Cash',
                'payment_status' => $booking->payment_status ?? 'pending',
                'pickup' => $booking->pickupLocation?->address ?? '',
                'drop' => $booking->dropLocation?->address ?? '',
                'created_at' => $booking->created_at?->toIso8601String(),
            ],
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

    public function dashboard(Request $request): JsonResponse
    {
        $driver = $request->user();

        if (! $driver instanceof Driver) {
            return ApiResponseHelper::error('Unauthorized.', null, 401);
        }

        $stats = $this->bookingService->getDriverStats($driver);

        $todayEarnings = Booking::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereDate('completed_at', now()->today())
            ->sum(DB::raw('COALESCE(NULLIF(final_amount, 0), estimated_amount)'));

        $todayTrips = Booking::where('driver_id', $driver->id)
            ->where('status', 'completed')
            ->whereDate('completed_at', now()->today())
            ->count();

        $recentBookings = Booking::where('driver_id', $driver->id)
            ->with([
                'category',
                'user',
                'pickupLocation',
                'dropLocation',
                'fare',
            ])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Dashboard fetched successfully.',
            'data' => [
                'is_online' => (bool) $driver->is_online,
                'today_earnings' => round((float) $todayEarnings, 2),
                'today_trips' => (int) $todayTrips,
                'average_rating' => (float) ($stats['average_rating'] ?? 4.9),
                'total_rides' => (int) ($stats['total_rides'] ?? 0),
                'recent_trips' => BookingResource::collection($recentBookings),
            ],
        ]);
    }

    public function toggleOnlineStatus(Request $request): JsonResponse
    {
        $driver = $request->user();

        if (! $driver instanceof Driver) {
            return ApiResponseHelper::error('Unauthorized.', null, 401);
        }

        $validated = $request->validate([
            'is_online' => ['required', 'boolean'],
        ]);

        $newStatus = (bool) $validated['is_online'];

        if (! $newStatus) {
            $activeBooking = $this->bookingService->driverActiveBooking($driver);
            if ($activeBooking) {
                return ApiResponseHelper::error(
                    'You cannot go offline while you have an active ride.',
                    null,
                    422
                );
            }
        }

        $driver->update([
            'is_online' => $newStatus,
        ]);

        return response()->json([
            'status' => true,
            'message' => $newStatus ? 'Driver is now online.' : 'Driver is now offline.',
            'data' => [
                'is_online' => (bool) $driver->is_online,
                'driver_id' => $driver->id,
            ],
        ]);
    }
}


