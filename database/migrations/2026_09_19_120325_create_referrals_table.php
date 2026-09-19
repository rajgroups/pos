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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('referrer_id');
            $table->string('referrer_type');
            
            $table->unsignedBigInteger('referred_id');
            $table->string('referred_type');
            
            $table->string('referral_code', 50);
            
            $table->enum('status', ['PENDING', 'REGISTERED', 'QUALIFIED', 'REWARDED', 'CANCELLED'])->default('REGISTERED');
            $table->decimal('reward_amount', 12, 2)->default(0);
            
            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();
            
            $table->timestamps();

            $table->index(['referrer_id', 'referrer_type']);
            $table->unique(['referred_id', 'referred_type']); // Enforce one referrer per entity
            $table->index('referral_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};

