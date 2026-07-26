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
        Schema::create('sos_alerts', function (Blueprint $table) {

            $table->id();

            // Foreign key to bookings table
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->cascadeOnDelete();

            // Foreign key to users table
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Type of SOS alert
            $table->enum('type', [
                'police',
                'ambulance',
                'emergency_contact',
                'safety_team',
            ])->index();

            // Optional location at time of SOS
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Optional message
            $table->text('message')->nullable();

            // Status
            $table->enum('status', ['active', 'resolved'])->default('active')->index();

            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            // Composite indexes for admin queries
            $table->index(['booking_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sos_alerts');
    }
};
