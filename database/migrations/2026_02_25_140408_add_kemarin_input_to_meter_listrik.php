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
        Schema::table('meter_listrik_readings', function (Blueprint $table) {
            $table->double('lwbp_awal')->nullable();
            $table->double('lwbp_akhir')->nullable();
            $table->double('pemakaian_lwbp')->nullable();
            $table->double('wbp_awal')->nullable();
            $table->double('wbp_akhir')->nullable();
            $table->double('pemakaian_wbp')->nullable();
            $table->double('kvarh_awal')->nullable();
            $table->double('kvarh_akhir')->nullable();
            $table->double('pemakaian_kvarh')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meter_listrik_readings', function (Blueprint $table) {
                    $table->double('lwbp')->nullable();
        $table->double('hasil_lwbp')->nullable();
        
        // WBP
        $table->double('wbp')->nullable();
        $table->double('hasil_wbp')->nullable();
        
        // KVARH
        $table->double('kvarh')->nullable();
        $table->double('hasil_kvarh')->nullable();
        
        });
    }
};
