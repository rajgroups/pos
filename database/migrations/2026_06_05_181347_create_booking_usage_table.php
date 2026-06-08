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
        Schema::create('booking_usage', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booking_id')
                ->unique()
                ->constrained('bookings')
                ->cascadeOnDelete();

            // Ride
            $table->decimal('distance_km', 12, 2)->default(0);

            // Rental
            $table->decimal('hours_used', 12, 2)->default(0);

            // Agriculture
            $table->decimal('acre_used', 12, 2)->default(0);

            // Transport
            $table->decimal('weight_ton', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_usage');
    }
};
