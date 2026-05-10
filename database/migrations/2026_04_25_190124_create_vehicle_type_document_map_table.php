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
        Schema::create('vehicle_type_document_map', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->cascadeOnDelete();

            $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->cascadeOnDelete();

            $table->boolean('is_required')->default(true);

            $table->timestamps();

            $table->unique(['vehicle_type_id', 'document_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_type_document_map');
    }
};
