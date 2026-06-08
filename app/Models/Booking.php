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

    protected $fillable = [
        'booking_no',
        'user_id',
        'driver_id',
        'vehicle_id',
        'vehicle_category_id',
        'service_mode',
        'scheduled_at',
        'duration_hours',
        'start_otp',
        'otp_verified_at',
        'status',
        'estimated_amount',
        'final_amount',
        'payment_method',
        'payment_status',
        'accepted_at',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_hours' => 'decimal:2',
        'otp_verified_at' => 'datetime',
        'estimated_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'accepted_at' => 'datetime',
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
