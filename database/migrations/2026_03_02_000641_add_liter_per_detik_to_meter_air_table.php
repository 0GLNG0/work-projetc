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
            $table->decimal('liter_per_detik', 12, 4)->nullable()->after('pemakaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meter_air_readings', function (Blueprint $table) {
            $table->dropColumn('liter_per_detik');
        });
    }
};
