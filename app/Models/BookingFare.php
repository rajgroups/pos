<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingFare extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'pricing_type',
        'base_fare',
        'unit_rate',
        'usage_amount',
        'extra_charge',
        'discount',
        'total_amount',
        'snapshot',
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'usage_amount' => 'decimal:2',
        'extra_charge' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'snapshot' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
