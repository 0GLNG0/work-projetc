<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('water_meter_readings', function (Blueprint $table) {
            $table->decimal('meter_air_sebelumnya', 10, 2)->nullable()->after('meter_air');
            $table->decimal('pemakaian_air', 10, 2)->nullable()->after('meter_air');
            $table->decimal('meter_listrik_sebelumnya', 10, 2)->nullable()->after('meter_listrik');
            $table->decimal('pemakaian_listrik', 10, 2)->nullable()->after('meter_listrik');
        });
    }

    public function down()
    {
        Schema::table('water_meter_readings', function (Blueprint $table) {
            $table->dropColumn([
                'meter_air_sebelumnya',
                'pemakaian_air',
                'meter_listrik_sebelumnya',
                'pemakaian_listrik'
            ]);
        });
    }
};