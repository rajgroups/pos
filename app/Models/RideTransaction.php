<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * RideTransaction
 *
 * Authoritative financial ledger record for a completed Indicab ride.
 *
 * One record per booking (enforced by UNIQUE booking_id DB constraint).
 * All monetary values are immutable after settlement — admin config changes
 * (commission %, tax %) do NOT affect historical records.
 *
 * @property int         $id
 * @property string      $transaction_ref        e.g. INC-TXN-20261001-000042
 * @property string      $transaction_uuid
 * @property int         $booking_id
 * @property int|null    $user_id
 * @property int|null    $driver_id
 * @property int|null    $vehicle_category_id
 * @property int|null    $parent_transaction_id
 * @property string      $transaction_type       ride_payment|ride_refund|…
 * @property string      $transaction_status     pending|completed|failed|refunded|cancelled
 * @property string|null $payment_method         cash|wallet|online|upi|card|other
 * @property string      $payment_status         pending|paid|failed|refunded
 * @property string      $currency               INR
 * @property string      $base_fare
 * @property string      $distance_fare
 * @property string      $time_fare
 * @property string      $waiting_fare
 * @property string      $extra_charges
 * @property string      $surge_amount
 * @property string      $discount_amount
 * @property string      $subtotal
 * @property string|null $tax_type
 * @property string      $tax_rate
 * @property string      $tax_amount
 * @property string|null $commission_type
 * @property string      $commission_rate
 * @property string      $commission_amount
 * @property string      $driver_earning
 * @property string      $platform_earning
 * @property string      $final_amount
 * @property string      $paid_amount
 * @property string      $refund_amount
 * @property string|null $distance_km
 * @property string|null $duration_minutes
 * @property string|null $waiting_minutes
 * @property string|null $notes
 * @property \Carbon\Carbon|null $settled_at
 */
class RideTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ride_transactions';

    // ── Transaction type constants ─────────────────────────────────────────────
    public const TYPE_RIDE_PAYMENT      = 'ride_payment';
    public const TYPE_RIDE_REFUND       = 'ride_refund';
    public const TYPE_CANCELLATION_FEE  = 'cancellation_fee';
    public const TYPE_DRIVER_PAYOUT     = 'driver_payout';
    public const TYPE_COMMISSION        = 'commission';
    public const TYPE_TAX               = 'tax';
    public const TYPE_ADJUSTMENT        = 'adjustment';

    // ── Transaction status constants ───────────────────────────────────────────
    public const STATUS_PENDING    = 'pending';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_FAILED     = 'failed';
    public const STATUS_REFUNDED   = 'refunded';
    public const STATUS_CANCELLED  = 'cancelled';

    // ── Payment method constants ───────────────────────────────────────────────
    public const METHOD_CASH    = 'cash';
    public const METHOD_WALLET  = 'wallet';
    public const METHOD_ONLINE  = 'online';
    public const METHOD_UPI     = 'upi';
    public const METHOD_CARD    = 'card';
    public const METHOD_OTHER   = 'other';

    // ── Payment status constants ───────────────────────────────────────────────
    public const PAYMENT_PENDING  = 'pending';
    public const PAYMENT_PAID     = 'paid';
    public const PAYMENT_FAILED   = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'transaction_ref',
        'transaction_uuid',
        'booking_id',
        'user_id',
        'driver_id',
        'vehicle_category_id',
        'parent_transaction_id',
        'transaction_type',
        'transaction_status',
        'payment_method',
        'payment_status',
        'currency',
        // Fare breakdown snapshot
        'base_fare',
        'distance_fare',
        'time_fare',
        'waiting_fare',
        'extra_charges',
        'surge_amount',
        'discount_amount',
        'subtotal',
        // Tax snapshot
        'tax_type',
        'tax_rate',
        'tax_amount',
        // Commission snapshot
        'commission_type',
        'commission_rate',
        'commission_amount',
        // Settlement
        'driver_earning',
        'platform_earning',
        'final_amount',
        'paid_amount',
        'refund_amount',
        // Trip metrics
        'distance_km',
        'duration_minutes',
        'waiting_minutes',
        // Audit
        'notes',
        'settlement_snapshot',
        'settled_at',
    ];

    protected $casts = [
        // Money — cast to string to avoid float rounding in PHP
        'base_fare'         => 'decimal:2',
        'distance_fare'     => 'decimal:2',
        'time_fare'         => 'decimal:2',
        'waiting_fare'      => 'decimal:2',
        'extra_charges'     => 'decimal:2',
        'surge_amount'      => 'decimal:2',
        'discount_amount'   => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'tax_rate'          => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'commission_rate'   => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'driver_earning'    => 'decimal:2',
        'platform_earning'  => 'decimal:2',
        'final_amount'      => 'decimal:2',
        'paid_amount'       => 'decimal:2',
        'refund_amount'     => 'decimal:2',
        // Trip metrics
        'distance_km'       => 'decimal:3',
        'duration_minutes'  => 'decimal:2',
        'waiting_minutes'   => 'decimal:2',
        'settlement_snapshot' => 'array',
        // Dates
        'settled_at'        => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicleCategory(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }

    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(RideTransaction::class, 'parent_transaction_id');
    }

    public function childTransactions(): HasMany
    {
        return $this->hasMany(RideTransaction::class, 'parent_transaction_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->where('transaction_status', self::STATUS_COMPLETED);
    }

    public function scopeRidePayments($query)
    {
        return $query->where('transaction_type', self::TYPE_RIDE_PAYMENT);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
                     ->whereMonth('created_at', now()->month);
    }

    // ── Computed accessors ─────────────────────────────────────────────────────

    /**
     * Gross fare = subtotal (before tax).
     * Alias for readability in views.
     */
    public function getGrossFareAttribute(): string
    {
        return $this->subtotal;
    }

    // ── Static helpers ─────────────────────────────────────────────────────────

    /**
     * Generate a human-readable, unique transaction reference.
     * Format: INC-TXN-YYYYMMDD-NNNNNN
     *
     * Uses the next auto-increment ID from the DB to guarantee uniqueness
     * without race conditions. If called before the row is persisted,
     * uses a random 6-digit suffix instead.
     *
     * @param  int|null $id  The model's own ID after creation
     */
    public static function generateRef(?int $id = null): string
    {
        $date   = now()->format('Ymd');
        $suffix = $id !== null
            ? str_pad($id, 6, '0', STR_PAD_LEFT)
            : str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);

        return "INC-TXN-{$date}-{$suffix}";
    }

    /**
     * Generate a UUID suitable for external/API use.
     */
    public static function generateUuid(): string
    {
        return (string) Str::uuid();
    }
}
