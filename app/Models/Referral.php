<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referrer_type',
        'referred_id',
        'referred_type',
        'referral_code',
        'status',
        'reward_amount',
        'qualified_at',
        'rewarded_at',
    ];

    protected $casts = [
        'qualified_at' => 'datetime',
        'rewarded_at' => 'datetime',
        'reward_amount' => 'decimal:2',
    ];

    public function referrer()
    {
        return $this->morphTo();
    }

    public function referred()
    {
        return $this->morphTo();
    }
}
