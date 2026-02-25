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
        Schema::create('meter_air_readings', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi');
            $table->date('tanggal');
            $table->time('jam');
            $table->decimal('meter_awal', 10, 2)->nullable();
            $table->decimal('meter_akhir', 10, 2);
            $table->decimal('pemakaian', 10, 2)->nullable();
            $table->string('foto')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status_meter')->nullable();
            $table->string('petugas');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('lokasi');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meter_air_readings');
    }
};
