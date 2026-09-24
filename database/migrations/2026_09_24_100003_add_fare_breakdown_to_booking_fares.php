<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Expand booking_fares with a full fare breakdown snapshot.
 *
 * The existing `snapshot` JSON column already stores a generic blob.
 * These dedicated columns make it trivial to query/audit any historical booking
 * without decoding JSON, and serve as source of truth for billing screens.
 *
 * All new columns are nullable so existing rows are unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_fares', function (Blueprint $table) {
            // ── Detailed fare breakdown ───────────────────────────────────
            $table->decimal('distance_charge', 12, 2)->default(0)->after('base_fare');
            $table->decimal('time_charge', 12, 2)->default(0)->after('distance_charge');
            $table->decimal('waiting_charge', 12, 2)->default(0)->after('time_charge');
            $table->decimal('subtotal', 12, 2)->default(0)->after('waiting_charge');

            // ── Commission (captured at booking time) ─────────────────────
            $table->string('commission_type', 20)->nullable()->after('subtotal');
            $table->decimal('commission_value', 8, 2)->nullable()->after('commission_type');
            $table->decimal('commission_amount', 12, 2)->default(0)->after('commission_value');

            // ── Tax ───────────────────────────────────────────────────────
            $table->decimal('tax_percentage', 5, 2)->default(0)->after('commission_amount');
            $table->decimal('tax_amount', 12, 2)->default(0)->after('tax_percentage');

            // ── Payables ──────────────────────────────────────────────────
            $table->decimal('user_total', 12, 2)->default(0)->after('tax_amount');
            $table->decimal('driver_earnings', 12, 2)->default(0)->after('user_total');

            // ── Trip metrics ──────────────────────────────────────────────
            $table->decimal('distance_km', 8, 3)->nullable()->after('driver_earnings');
            $table->decimal('duration_minutes', 8, 2)->nullable()->after('distance_km');
            $table->decimal('waiting_minutes', 8, 2)->nullable()->after('duration_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('booking_fares', function (Blueprint $table) {
            $table->dropColumn([
                'distance_charge',
                'time_charge',
                'waiting_charge',
                'subtotal',
                'commission_type',
                'commission_value',
                'commission_amount',
                'tax_percentage',
                'tax_amount',
                'user_total',
                'driver_earnings',
                'distance_km',
                'duration_minutes',
                'waiting_minutes',
            ]);
        });
    }
};
