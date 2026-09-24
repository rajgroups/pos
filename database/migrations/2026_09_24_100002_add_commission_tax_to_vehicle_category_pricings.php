<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add commission and tax fields to vehicle_category_pricings.
 *
 * commission_type  – 'percentage' | 'fixed'
 * commission_value – the percentage (0–100) or fixed INR amount
 * tax_percentage   – GST/tax percentage applied to user fare
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_category_pricings', function (Blueprint $table) {
            $table->string('commission_type', 20)
                ->default('percentage')
                ->after('surge_multiplier')
                ->comment('fixed | percentage');

            $table->decimal('commission_value', 8, 2)
                ->default(0.00)
                ->after('commission_type')
                ->comment('% (0–100) for percentage type, INR for fixed type');

            $table->decimal('tax_percentage', 5, 2)
                ->default(0.00)
                ->after('commission_value')
                ->comment('GST/tax percentage applied to subtotal for user');

            $table->index(['commission_type']);
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_category_pricings', function (Blueprint $table) {
            $table->dropIndex(['commission_type']);
            $table->dropColumn(['commission_type', 'commission_value', 'tax_percentage']);
        });
    }
};
