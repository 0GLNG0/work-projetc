<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meter_listrik_readings', function (Blueprint $table) {
            $table->string('nomor_id')->nullable()->after('lokasi');
        });
    }

    public function down()
    {
        Schema::table('meter_listrik_readings', function (Blueprint $table) {
            $table->dropColumn('nomor_id');
        });
    }
};