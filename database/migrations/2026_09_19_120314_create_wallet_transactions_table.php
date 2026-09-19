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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id');
            $table->string('user_type')->default('App\\\\Models\\\\User');
            
            $table->string('type', 50);
            $table->decimal('amount', 12, 2);
            $table->string('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'user_type']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};

