<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController 
{
    public function home(){
        return view('home');
    }

//     public function readingsGabungan(Request $request)
// {
//     $query = \App\Models\MeterReading::query(); // (Sesuaikan query filter kamu)
    
//     // 1. Ambil data asli (Ini yang dicari sama view kamu yang lama)
//     $readings = $query->orderBy('tanggal', 'desc')->get();
    
//     // 2. Buat data yang sudah dikelompokkan
//     $groupedReadings = $readings->groupBy('nama_lokasi');
    
//     // 3. KIRIM KEDUANYA KE VIEW (Perhatikan compact-nya)
//     return view('home', compact('readings', 'groupedReadings'));
// }
}
