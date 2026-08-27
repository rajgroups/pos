<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Booking lifecycle statuses used across the app.
     *
     * Stored status values:
     * pending, requested, accepted, arrived, started, completed, cancelled,
     * expired, no_driver_available, timeout.
     *
     * Transitional / legacy aliases that appear in filters or dispatch flows:
     * assigned, dispatched, scheduled, searching_driver, in_progress.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_REQUESTED = 'requested';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_DISPATCHED = 'dispatched';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_ARRIVED = 'arrived';
    public const STATUS_STARTED = 'started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_NO_DRIVER_AVAILABLE = 'no_driver_available';
    public const STATUS_TIMEOUT = 'timeout';
    public const STATUS_SEARCHING_DRIVER = 'searching_driver';

    /**
     * Status groups used by booking filters and background dispatch logic.
     */
    public const ACTIVE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_REQUESTED,
        self::STATUS_ASSIGNED,
        self::STATUS_DISPATCHED,
        self::STATUS_ACCEPTED,
        self::STATUS_ARRIVED,
        self::STATUS_STARTED,
        self::STATUS_SEARCHING_DRIVER,
    ];

    public const IN_PROGRESS_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_REQUESTED,
        self::STATUS_ASSIGNED,
        self::STATUS_DISPATCHED,
        self::STATUS_ACCEPTED,
        self::STATUS_ARRIVED,
        self::STATUS_STARTED,
    ];

    public const TERMINAL_STATUSES = [
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
        self::STATUS_EXPIRED,
        self::STATUS_NO_DRIVER_AVAILABLE,
        self::STATUS_TIMEOUT,
    ];

    protected $fillable = [
        'booking_no',
        'user_id',
        'driver_id',
        'vehicle_id',
        'vehicle_category_id',
        'service_mode',
        'scheduled_at',
        'scheduled_processed_at',
        'scheduled_notification_sent_at',
        'driver_search_started_at',
        'duration_hours',
        'start_otp',
        'otp_verified_at',
        'status',
        'driver_response_expires_at',
        'estimated_amount',
        'final_amount',
        'payment_method',
        'payment_status',
        'accepted_at',
        'arrived_at',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'scheduled_processed_at' => 'datetime',
        'scheduled_notification_sent_at' => 'datetime',
        'driver_search_started_at' => 'datetime',
        'duration_hours' => 'decimal:2',
        'otp_verified_at' => 'datetime',
        'driver_response_expires_at' => 'datetime',
        'estimated_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'accepted_at' => 'datetime',
        'arrived_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $appends = [
        'category_name',
        'pickup_address',
        'drop_address',
    ];

    public function getRouteKeyName(): string
    {
        return 'booking_no';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(BookingLocation::class)->orderBy('sequence');
    }

    public function pickupLocation(): HasOne
    {
        return $this->hasOne(BookingLocation::class)->where('location_type', 'pickup');
    }

    public function dropLocation(): HasOne
    {
        return $this->hasOne(BookingLocation::class)->where('location_type', 'drop');
    }

    public function usage(): HasOne
    {
        return $this->hasOne(BookingUsage::class);
    }

    public function fare(): HasOne
    {
        return $this->hasOne(BookingFare::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function userReview(): HasOne
    {
        return $this->hasOne(Review::class)->where('reviewed_by', 'user');
    }

    public function sosAlerts(): HasMany
    {
        return $this->hasMany(SosAlert::class)->latest();
    }

    public function getCategoryNameAttribute(): ?string
    {
        return $this->category?->name;
    }

    public function getPickupAddressAttribute(): ?string
    {
        return $this->pickupLocation?->address;
    }

    public function getDropAddressAttribute(): ?string
    {
        return $this->dropLocation?->address;
    }
}
