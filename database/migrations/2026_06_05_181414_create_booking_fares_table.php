<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_fares', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booking_id')
                ->unique()
                ->constrained('bookings')
                ->cascadeOnDelete();

            $table->string('pricing_type');

            $table->decimal('base_fare', 12, 2)->default(0);

            $table->decimal('unit_rate', 12, 2)->default(0);

            $table->decimal('usage_amount', 12, 2)->default(0);

            $table->decimal('extra_charge', 12, 2)->default(0);

            $table->decimal('discount', 12, 2)->default(0);

            $table->decimal('total_amount', 12, 2)->default(0);

            $table->json('snapshot')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_fares');
    }
};
