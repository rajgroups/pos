<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the ride_transactions table.
 *
 * This is the authoritative financial ledger for every completed Indicab ride.
 *
 * Design principles:
 *  - One row per booking (UNIQUE booking_id) → idempotency enforced at DB level.
 *  - All monetary columns are DECIMAL(12,2) — never FLOAT.
 *  - Commission + tax configuration is SNAPSHOTTED at settlement time.
 *    Changing vehicle-category pricing later does NOT alter historical records.
 *  - Separate refund transactions reference the original via parent_transaction_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ride_transactions', function (Blueprint $table) {

            $table->id();

            // ── Human-friendly reference ──────────────────────────────────────
            // e.g. INC-TXN-20261001-000042 — unique, indexed, searchable
            $table->string('transaction_ref', 40)->unique();

            // ── UUID for external / API use ───────────────────────────────────
            $table->uuid('transaction_uuid')->unique();

            // ── Relations ─────────────────────────────────────────────────────
            $table->foreignId('booking_id')
                ->nullable()
                ->unique()                        // ONE transaction per booking (nulls allowed for refunds)
                ->constrained('bookings')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete();

            $table->foreignId('vehicle_category_id')
                ->nullable()
                ->constrained('vehicle_categories')
                ->nullOnDelete();

            // ── Self-referencing for refunds / adjustments ────────────────────
            // A refund row points back to the original ride_payment row.
            $table->foreignId('parent_transaction_id')
                ->nullable()
                ->constrained('ride_transactions')
                ->nullOnDelete();

            // ── Transaction metadata ──────────────────────────────────────────
            // transaction_type: ride_payment | ride_refund | cancellation_fee |
            //                   driver_payout | commission | tax | adjustment
            $table->string('transaction_type', 30)->default('ride_payment');

            // transaction_status: pending | completed | failed | refunded | cancelled
            $table->string('transaction_status', 30)->default('pending');

            // payment_method: cash | wallet | online | upi | card | other
            $table->string('payment_method', 30)->nullable();

            // payment_status: pending | paid | failed | refunded
            $table->string('payment_status', 30)->default('pending');

            $table->string('currency', 10)->default('INR');

            // ── Fare breakdown snapshot ───────────────────────────────────────
            $table->decimal('base_fare', 12, 2)->default(0);
            $table->decimal('distance_fare', 12, 2)->default(0);  // distance_charge
            $table->decimal('time_fare', 12, 2)->default(0);      // time_charge
            $table->decimal('waiting_fare', 12, 2)->default(0);   // waiting_charge
            $table->decimal('extra_charges', 12, 2)->default(0);  // extra_charge
            $table->decimal('surge_amount', 12, 2)->default(0);   // night_surge
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);       // pre-tax gross fare

            // ── Tax snapshot ──────────────────────────────────────────────────
            // Stored at settlement time — immutable historical record.
            $table->string('tax_type', 50)->nullable();           // e.g. 'GST', 'VAT'
            $table->decimal('tax_rate', 5, 2)->default(0);        // % used at settlement
            $table->decimal('tax_amount', 12, 2)->default(0);

            // ── Commission snapshot ───────────────────────────────────────────
            // Stored at settlement time — immutable historical record.
            $table->string('commission_type', 20)->nullable();    // 'percentage' | 'fixed'
            $table->decimal('commission_rate', 8, 2)->default(0); // % or fixed INR
            $table->decimal('commission_amount', 12, 2)->default(0);

            // ── Settlement amounts ────────────────────────────────────────────
            $table->decimal('driver_earning', 12, 2)->default(0);
            $table->decimal('platform_earning', 12, 2)->default(0); // commission_amount
            $table->decimal('final_amount', 12, 2)->default(0);     // user_total (subtotal + tax)
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('refund_amount', 12, 2)->default(0);

            // ── Trip metrics (snapshot) ───────────────────────────────────────
            $table->decimal('distance_km', 8, 3)->nullable();
            $table->decimal('duration_minutes', 8, 2)->nullable();
            $table->decimal('waiting_minutes', 8, 2)->nullable();

            // ── Additional notes / audit ──────────────────────────────────────
            $table->text('notes')->nullable();
            $table->timestamp('settled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ── Indexes for performance ───────────────────────────────────────
            $table->index('transaction_type');
            $table->index('transaction_status');
            $table->index('payment_method');
            $table->index('payment_status');
            $table->index('currency');
            $table->index('user_id');
            $table->index('driver_id');
            $table->index('vehicle_category_id');
            $table->index('created_at');
            $table->index('settled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ride_transactions');
    }
};
