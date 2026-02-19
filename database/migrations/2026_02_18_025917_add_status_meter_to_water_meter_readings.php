<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('water_meter_readings', function (Blueprint $table) {
            // Tambah kolom status_meter
            $table->string('status_meter')->nullable()->after('keterangan');
            
            // Pastikan kolom lain juga ada (cek satu per satu)
            if (!Schema::hasColumn('water_meter_readings', 'petugas')) {
                $table->string('petugas')->nullable()->after('status_meter');
            }
            
            if (!Schema::hasColumn('water_meter_readings', 'meter_air_sebelumnya')) {
                $table->decimal('meter_air_sebelumnya', 10, 2)->nullable()->after('meter_air');
            }
            
            if (!Schema::hasColumn('water_meter_readings', 'pemakaian_air')) {
                $table->decimal('pemakaian_air', 10, 2)->nullable()->after('meter_air_sebelumnya');
            }
            
            if (!Schema::hasColumn('water_meter_readings', 'meter_listrik_sebelumnya')) {
                $table->decimal('meter_listrik_sebelumnya', 10, 2)->nullable()->after('meter_listrik');
            }
            
            if (!Schema::hasColumn('water_meter_readings', 'pemakaian_listrik')) {
                $table->decimal('pemakaian_listrik', 10, 2)->nullable()->after('meter_listrik_sebelumnya');
            }
        });
    }

    public function down()
    {
        Schema::table('water_meter_readings', function (Blueprint $table) {
            $table->dropColumn([
                'status_meter',
                'petugas',
                'meter_air_sebelumnya',
                'pemakaian_air',
                'meter_listrik_sebelumnya',
                'pemakaian_listrik'
            ]);
        });
    }
};