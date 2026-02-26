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
        Schema::table('meter_air_readings', function (Blueprint $table) {
            // Tambahan liter/detik (biasanya masuk ke tabel air)
        $table->double('liter_per_detik')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meter_air_readings', function (Blueprint $table) {
            //
        });
    }
};
