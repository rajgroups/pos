<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'otp',
        'email',
        'dob',
        'gender',
        'wallet_balance',
        'address',
        'city',
        'state',
        'pincode',
        'aadhaar_number',
        'pan_number',
        'license_number',
        'license_expiry',
        'license_categories',
        'driver_type',
        'status',
        'is_online',
        'fcm_token',
        'is_verified',
        'profile_photo',
        'license_front',
        'license_back',
        'aadhaar_front',
        'aadhaar_back',
        'pan_card_file',
        'police_verification_file',
        'medical_certificate',
        'remarks',
        'referral_code',
        'referred_by_id',
        'referred_by_type',
    ];

    protected $casts = [
        'dob' => 'date',
        'license_expiry' => 'date',
        'license_categories' => 'array',
        'is_verified' => 'boolean',
        'is_online' => 'boolean',
        'wallet_balance' => 'decimal:2',
    ];


    public function documents(): HasMany
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class, 'driver_id');
    }

    public function rechargeRequests(): HasMany
    {
        return $this->hasMany(WalletRechargeRequest::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 2) : 4.9;
    }

    public function walletTransactions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(WalletTransaction::class, 'user');
    }

    public function referralsMade(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Referral::class, 'referrer');
    }

    public function referralReceived(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Referral::class, 'referred');
    }
}
