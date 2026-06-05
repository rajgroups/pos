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
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('location_type', [
                'pickup',
                'drop',
                'stop',
                'worksite'
            ]);

            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);

            $table->string('address')->nullable();

            $table->integer('sequence')->default(1);

            $table->timestamps();

            $table->index([
                'booking_id',
                'sequence'
            ]);
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
