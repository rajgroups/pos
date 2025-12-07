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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('slug')->unique()->nullable();
            $table->string('owner_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('currency', 10)->default('INR');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->json('gallery')->nullable();
            $table->json('opening_hours')->nullable();
            $table->json('social_media')->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('status');
            $table->index('email');
            $table->index('phone');
            $table->index('created_at');
        });

        // Add fulltext index for search
        Schema::table('stores', function (Blueprint $table) {
            $table->fullText(['store_name', 'owner_name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
