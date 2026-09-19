<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'country_code',
        'password',
        'otp',
        'uuid',
        'gender',
        'date_of_birth',
        'profile_image',
        'address',
        'city',
        'state',
        'postal_code',
        'wallet_balance',
        'device_token',
        'fcm_token',
        'referral_code',
        'referred_by',
        'referred_by_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class);
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
