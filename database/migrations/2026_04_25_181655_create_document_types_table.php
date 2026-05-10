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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            // Aadhaar, PAN, Driving License, RC, Insurance

            $table->string('slug')->unique();

            $table->enum('for_type', ['driver', 'vehicle']);
            // Which module this document belongs to

            $table->boolean('has_expiry')->default(false);

            $table->boolean('is_required')->default(true);

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
