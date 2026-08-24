<?php

namespace App\Repositories;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;


class BookingRepository
{
    /**
     * The booking model instance.
     *
     * @var \App\Models\Booking
     */
    protected $model;

    /**
     * Create a new repository instance.
     *
     * @param \App\Models\Booking $model
     * @return void
     */
    public function __construct(Booking $model)
    {
        $this->model = $model;
    }

    /**
     * Find an active booking for a given user ID.
     *
     * @param int $userId
     * @return \App\Models\Booking|null
     */
    public function findActiveBookingByUserId(int $userId): ?Booking
    {
        return $this->model->where('user_id', $userId)
            ->whereNotIn('status', Booking::TERMINAL_STATUSES)
            ->latest()
            ->first();
    }

    /**
     * Check if a driver has an active booking.
     *
     * @param int $driverId
     * @param int $excludeBookingId
     * @return bool
     */
    public function hasActiveDriverBooking(int $driverId, int $excludeBookingId): bool
    {
        return $this->model->where('driver_id', $driverId)
            ->where('id', '!=', $excludeBookingId)
            ->whereNotIn('status', Booking::TERMINAL_STATUSES)
            ->exists();
    }

    /**
     * Check if a vehicle has an active booking.
     *
     * @param int $vehicleId
     * @param int $excludeBookingId
     * @return bool
     */
    public function hasActiveVehicleBooking(int $vehicleId, int $excludeBookingId): bool
    {
        return $this->model->where('vehicle_id', $vehicleId)
            ->where('id', '!=', $excludeBookingId)
            ->whereNotIn('status', Booking::TERMINAL_STATUSES)
            ->exists();
    }

    /**
     * Get paginated bookings for a user with status, date, search, and sorting filters.
     *
     * @param int $userId
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserBookings(int $userId, array $filters = [])
    {
        $query = $this->model->where('user_id', $userId)
            ->with([
                'category.pricing',
                'user',
                'locations',
                'pickupLocation',
                'dropLocation',
                'usage',
                'fare',
                'driver',
                'vehicle',
            ]);

        // Status Filter
        $status = strtolower(trim($filters['status'] ?? 'all'));
        if ($status !== 'all') {
            switch ($status) {
                case 'ongoing':
                case 'active':
                case 'in_progress':
                    $query->whereIn('status', Booking::IN_PROGRESS_STATUSES);
                    break;

                case Booking::STATUS_COMPLETED:
                    $query->where('status', Booking::STATUS_COMPLETED);
                    break;

                case Booking::STATUS_CANCELLED:
                case 'canceled':
                    $query->where('status', Booking::STATUS_CANCELLED);
                    break;

                case 'missed':
                case Booking::STATUS_EXPIRED:
                    $query->whereIn('status', [
                        Booking::STATUS_EXPIRED,
                        Booking::STATUS_NO_DRIVER_AVAILABLE,
                        Booking::STATUS_TIMEOUT,
                    ]);
                    break;

                default:
                    $query->where('status', $status);
                    break;
            }
        }

        // Search Filter (Booking No, Pickup Address, Drop Address)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('booking_no', 'like', "%{$search}%")
                  ->orWhereHas('pickupLocation', function ($lq) use ($search) {
                      $lq->where('address', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dropLocation', function ($dlq) use ($search) {
                      $dlq->where('address', 'like', "%{$search}%");
                  });
            });
        }

        // Date Range Filters
        if (!empty($filters['date_filter'])) {
            $dateFilter = strtolower(trim($filters['date_filter']));
            if ($dateFilter === 'today') {
                $query->whereDate('created_at', now()->today());
            } elseif ($dateFilter === 'this_week' || $dateFilter === 'this week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateFilter === 'this_month' || $dateFilter === 'this month') {
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            }
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        // Payment Method Filter
        if (!empty($filters['payment_method']) && strtolower($filters['payment_method']) !== 'all') {
            $query->where('payment_method', strtolower($filters['payment_method']));
        }

        // Vehicle Category Filter
        if (!empty($filters['vehicle_category_id'])) {
            $query->where('vehicle_category_id', $filters['vehicle_category_id']);
        }

        // Sorting
        $sortBy = strtolower(trim($filters['sort_by'] ?? 'newest'));
        if ($sortBy === 'oldest' || $sortBy === 'date: oldest') {
            $query->oldest();
        } elseif ($sortBy === 'price: high to low' || $sortBy === 'amount_high') {
            $query->orderByDesc('estimated_amount');
        } elseif ($sortBy === 'price: low to high' || $sortBy === 'amount_low') {
            $query->orderBy('estimated_amount');
        } else {
            $query->latest();
        }

        $perPage = (int) ($filters['per_page'] ?? $filters['limit'] ?? 15);

        return $query->paginate($perPage);
    }

    /**
     * Get paginated bookings for a driver with status, date, type, payment, price range, and sorting filters.
     *
     * @param int $driverId
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDriverBookings(int $driverId, array $filters = [])
    {
        $query = $this->model->where('driver_id', $driverId)
            ->with([
                'category.pricing',
                'user',
                'locations',
                'pickupLocation',
                'dropLocation',
                'usage',
                'fare',
                'driver',
                'vehicle',
            ]);

        // Status Filter
        $status = strtolower(trim($filters['status'] ?? 'all'));
        if ($status !== 'all') {
            switch ($status) {
                case 'ongoing':
                case 'active':
                case 'in_progress':
                    $query->whereIn('status', Booking::IN_PROGRESS_STATUSES);
                    break;

                case Booking::STATUS_COMPLETED:
                    $query->where('status', Booking::STATUS_COMPLETED);
                    break;

                case Booking::STATUS_CANCELLED:
                case 'canceled':
                    $query->where('status', Booking::STATUS_CANCELLED);
                    break;

                case 'missed':
                case Booking::STATUS_EXPIRED:
                    $query->whereIn('status', [
                        Booking::STATUS_EXPIRED,
                        Booking::STATUS_NO_DRIVER_AVAILABLE,
                        Booking::STATUS_TIMEOUT,
                    ]);
                    break;

                default:
                    $query->where('status', $status);
                    break;
            }
        }

        // Search Filter (Booking No, Pickup Address, Drop Address, User Name)
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('booking_no', 'like', "%{$search}%")
                  ->orWhereHas('pickupLocation', function ($lq) use ($search) {
                      $lq->where('address', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dropLocation', function ($dlq) use ($search) {
                      $dlq->where('address', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Date Range Filters
        if (!empty($filters['date_filter'])) {
            $dateFilter = strtolower(trim($filters['date_filter']));
            if ($dateFilter === 'today') {
                $query->whereDate('created_at', now()->today());
            } elseif ($dateFilter === 'this_week' || $dateFilter === 'this week') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($dateFilter === 'this_month' || $dateFilter === 'this month') {
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            }
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        // Payment Method Filter
        if (!empty($filters['payment_method']) && strtolower($filters['payment_method']) !== 'all') {
            $query->where('payment_method', strtolower($filters['payment_method']));
        }

        // Vehicle Category / Type Filter
        if (!empty($filters['vehicle_category_id'])) {
            $query->where('vehicle_category_id', $filters['vehicle_category_id']);
        } elseif (!empty($filters['type']) && strtolower($filters['type']) !== 'all') {
            $type = strtolower(trim($filters['type']));
            $query->whereHas('category', function ($cq) use ($type) {
                $cq->whereRaw('LOWER(name) LIKE ?', ["%{$type}%"]);
            });
        }

        // Min & Max Price Filters
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where(function ($pq) use ($filters) {
                $pq->where('final_amount', '>=', (float)$filters['min_price'])
                   ->orWhere(function ($epq) use ($filters) {
                       $epq->where('final_amount', 0)
                          ->where('estimated_amount', '>=', (float)$filters['min_price']);
                   });
            });
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price']) && (float)$filters['max_price'] > 0) {
            $query->where(function ($pq) use ($filters) {
                $pq->where(function ($fpq) use ($filters) {
                    $fpq->where('final_amount', '>', 0)
                        ->where('final_amount', '<=', (float)$filters['max_price']);
                })->orWhere(function ($epq) use ($filters) {
                    $epq->where('final_amount', 0)
                        ->where('estimated_amount', '<=', (float)$filters['max_price']);
                });
            });
        }

        // Sorting
        $sortBy = strtolower(trim($filters['sort_by'] ?? 'newest'));
        if ($sortBy === 'oldest' || $sortBy === 'date: oldest') {
            $query->oldest();
        } elseif ($sortBy === 'price: high to low' || $sortBy === 'amount_high') {
            $query->orderByRaw('COALESCE(NULLIF(final_amount, 0), estimated_amount) DESC');
        } elseif ($sortBy === 'price: low to high' || $sortBy === 'amount_low') {
            $query->orderByRaw('COALESCE(NULLIF(final_amount, 0), estimated_amount) ASC');
        } else {
            $query->latest();
        }

        $perPage = (int) ($filters['per_page'] ?? $filters['limit'] ?? 15);

        return $query->paginate($perPage);
    }

    /**
     * Get summary stats for a driver (total rides, total earnings, average rating).
     *
     * @param int $driverId
     * @return array
     */
    public function getDriverStats(int $driverId): array
    {
        $totalRides = $this->model->where('driver_id', $driverId)
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();

        $totalEarnings = (float) $this->model->where('driver_id', $driverId)
            ->where('status', Booking::STATUS_COMPLETED)
            ->sum(DB::raw('COALESCE(NULLIF(final_amount, 0), estimated_amount)'));

        $avgRating = \App\Models\Review::where('driver_id', $driverId)->avg('rating');
        $avgRating = $avgRating ? round((float) $avgRating, 2) : 4.9;

        return [
            'total_rides' => $totalRides,
            'total_spent' => round($totalEarnings, 2),
            'total_earnings' => round($totalEarnings, 2),
            'average_rating' => $avgRating,
        ];
    }
}
