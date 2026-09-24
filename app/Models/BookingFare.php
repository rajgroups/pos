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
        // Fare components
        'base_fare',
        'unit_rate',
        'usage_amount',
        'distance_charge',
        'time_charge',
        'waiting_charge',
        'extra_charge',
        'discount',
        'subtotal',
        'total_amount',
        // Commission snapshot
        'commission_type',
        'commission_value',
        'commission_amount',
        // Tax snapshot
        'tax_percentage',
        'tax_amount',
        // Final payables
        'user_total',
        'driver_earnings',
        // Trip metrics
        'distance_km',
        'duration_minutes',
        'waiting_minutes',
        // Full pricing snapshot (JSON blob for historical audit)
        'snapshot',
    ];

    protected $casts = [
        'base_fare'        => 'decimal:2',
        'unit_rate'        => 'decimal:2',
        'usage_amount'     => 'decimal:2',
        'distance_charge'  => 'decimal:2',
        'time_charge'      => 'decimal:2',
        'waiting_charge'   => 'decimal:2',
        'extra_charge'     => 'decimal:2',
        'discount'         => 'decimal:2',
        'subtotal'         => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'commission_value' => 'decimal:2',
        'commission_amount'=> 'decimal:2',
        'tax_percentage'   => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'user_total'       => 'decimal:2',
        'driver_earnings'  => 'decimal:2',
        'distance_km'      => 'decimal:3',
        'duration_minutes' => 'decimal:2',
        'waiting_minutes'  => 'decimal:2',
        'snapshot'         => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
