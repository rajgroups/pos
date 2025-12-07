<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warranties', function (Blueprint $table) {
            $table->id();

            // Warranty Main Fields
            $table->string('warranty');                     // Warranty Name
            $table->string('type');                         // Standard/Extended/Replacement/Service Warranty
            $table->string('code')->nullable();             // Optional warranty code

            // Duration & Period
            $table->integer('duration')->nullable();        // E.g., 12
            $table->string('period')->nullable();           // Day / Week / Month / Year

            // Start after X days
            $table->integer('start_after')->default(0);

            // Lifetime Warranty
            $table->boolean('lifetime')->default(0);

            // Claims & Replacement
            $table->integer('max_claims')->nullable();
            $table->boolean('replacement_allowed')->default(0);

            // Description & Terms
            $table->text('description');
            $table->longText('terms')->nullable();

            // Status
            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->softDeletes();   // <-- ADD THIS
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};
