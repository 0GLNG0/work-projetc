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
        Schema::create('water_meter_readings', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi');
            $table->time('jam');
            $table->date('tanggal');
            $table->decimal('meter_air', 10, 2);
            $table->decimal('meter_listrik', 10, 2);
            $table->string('foto')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_meter_readings');
    }
};
