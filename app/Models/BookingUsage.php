<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'distance_km',
        'hours_used',
        'acre_used',
        'weight_ton',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'hours_used' => 'decimal:2',
        'acre_used' => 'decimal:2',
        'weight_ton' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
