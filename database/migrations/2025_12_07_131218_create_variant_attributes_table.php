<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();

            // Basic fields
            $table->string('name');
            $table->string('slug')->unique();

            // Variant type (text, color, size, material)
            $table->enum('type', ['text', 'color', 'size', 'material'])
                  ->default('text');

            // Comma separated OR JSON values
            $table->json('values')->nullable();

            // Description
            $table->text('description')->nullable();

            // Sorting
            $table->integer('sort_order')->nullable();

            // Status: 1=active, 0=inactive
            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->softDeletes(); // Recommended for e-commerce platforms
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_attributes');
    }
};
