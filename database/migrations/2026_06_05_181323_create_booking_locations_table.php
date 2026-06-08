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
        Schema::create('booking_locations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            $table->string('location_type', 30)->index();

            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);

            $table->text('address')->nullable();

            $table->integer('sequence')->default(1);

            $table->timestamps();

            $table->index(['booking_id', 'sequence']);
            $table->index(['booking_id', 'location_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_locations');
    }
};
