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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referred_by_type')->nullable()->after('referred_by');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->string('referral_code', 50)->nullable()->after('wallet_balance');
            $table->unsignedBigInteger('referred_by_id')->nullable()->after('referral_code');
            $table->string('referred_by_type')->nullable()->after('referred_by_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_and_drivers', function (Blueprint $table) {
            //
        });
    }
};
