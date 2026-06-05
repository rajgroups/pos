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
        Schema::table('vehicle_categories', function (Blueprint $table) {
            $table->text('starting_fare')->nullable()->after('tagline');
            $table->text('price_label')->nullable()->after('starting_fare');
            $table->text('eta')->nullable()->after('price_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_categories', function (Blueprint $table) {
            $table->dropColumn(['starting_fare', 'price_label', 'eta']);
        });
    }
};
