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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('cascade');
            $table->unsignedTinyInteger('rating')->comment('Rating from 1 to 5 stars');
            $table->text('comment')->nullable();
            $table->json('feedback_tags')->nullable()->comment('Array of quick feedback tags');
            $table->string('reviewed_by')->default('user')->comment('user or driver');
            $table->timestamps();

            $table->unique(['booking_id', 'reviewed_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
