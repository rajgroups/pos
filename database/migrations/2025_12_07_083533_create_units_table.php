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
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();          // Unit Name (Kg, Liter, Meter)
            $table->string('shortname')->unique();     // Short Name (KG, L, M)
            $table->integer('no_of_product')->default(1);
            $table->enum('type', ['weight', 'length', 'volume', 'quantity'])
                  ->nullable()
                  ->comment('Optional unit category');

            $table->text('description')->nullable();

            $table->boolean('status')->default(true);  // 1 = Active, 0 = Inactive

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
