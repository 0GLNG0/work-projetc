<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lokasi;

class LokasiSeeder extends Seeder
{
// Ganti bagian Faker dengan input manual saja
public function run(): void
{
    $data = [
        ['nama_lokasi' => 'Kuwak 1', 'waktu_aktif_pompa' => 22],
        ['nama_lokasi' => 'Kuwak 2', 'waktu_aktif_pompa' => 22],
        ['nama_lokasi' => 'Wilis Utara', 'waktu_aktif_pompa' => 21],
        ['nama_lokasi' => 'tosaren', 'waktu_aktif_pompa' => 15],
    ];

    foreach ($data as $item) {
        // Pakai Model langsung, jangan pakai factory() atau fake()
        \App\Models\Lokasi::updateOrCreate(
            ['nama_lokasi' => $item['nama_lokasi']], 
            ['waktu_aktif_pompa' => $item['waktu_aktif_pompa']]
        );
    }
}
}