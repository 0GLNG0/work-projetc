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
        $oldData = DB::table('water_meter_readings')->get();
         foreach ($oldData as $data) {
            // Insert ke tabel air
            if ($data->meter_air) {
                DB::table('meter_air_readings')->insert([
                    'lokasi' => $data->lokasi,
                    'tanggal' => $data->tanggal,
                    'jam' => $data->jam,
                    'meter_awal' => $data->meter_air_sebelumnya,
                    'meter_akhir' => $data->meter_air,
                    'pemakaian' => $data->pemakaian_air,
                    'foto' => $data->foto,
                    'keterangan' => $data->keterangan,
                    'status_meter' => $data->status_meter,
                    'petugas' => $data->petugas,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ]);
            }
            
            // Insert ke tabel listrik
            if ($data->meter_listrik) {
                DB::table('meter_listrik_readings')->insert([
                    'lokasi' => $data->lokasi,
                    'tanggal' => $data->tanggal,
                    'jam' => $data->jam,
                    'meter_awal' => $data->meter_listrik_sebelumnya,
                    'meter_akhir' => $data->meter_listrik,
                    'pemakaian' => $data->pemakaian_listrik,
                    'foto' => $data->foto,
                    'keterangan' => $data->keterangan,
                    'status_meter' => $data->status_meter,
                    'petugas' => $data->petugas,
                    'created_at' => $data->created_at,
                    'updated_at' => $data->updated_at,
                ]);
                }
                }}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('meter_air_readings')->truncate();
        DB::table('meter_listrik_readings')->truncate();
    }
};
