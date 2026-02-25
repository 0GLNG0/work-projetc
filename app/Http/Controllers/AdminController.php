<?php

namespace App\Http\Controllers;

use App\Models\MeterAir;
use App\Models\MeterListrik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalAir = MeterAir::count();
        $totalListrik = MeterListrik::count();
        $todayAir = MeterAir::whereDate('tanggal', today())->count();
        $todayListrik = MeterListrik::whereDate('tanggal', today())->count();
        return view('admin.dashboard', compact('totalAir', 'totalListrik', 'todayAir', 'todayListrik'));

    }

public function readingsAir(Request $request)
{
    $query = \App\Models\MeterAir::query();

    // 1. FILTER LOKASI (Perhatikan 'nama_lokasi')
    if ($request->filled('lokasi')) {
        // Coba pakai 'nama_lokasi' kalau kolom di DB namanya itu
        // Jika kolom di DB beneran 'lokasi', ubah kembali jadi 'lokasi'
        $query->where('lokasi', $request->lokasi); 
    }

    // 2. FILTER STATUS METER
    if ($request->filled('status_meter')) {
        $query->where('status_meter', $request->status_meter);
    }

    // 3. FILTER TANGGAL MULAI
    if ($request->filled('tanggal_mulai')) {
        $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
    }

    // 4. FILTER TANGGAL SELESAI
    if ($request->filled('tanggal_selesai')) {
        $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
    }

    // 5. FILTER PETUGAS
    if ($request->filled('petugas')) {
        $query->where('petugas', 'like', '%' . $request->petugas . '%');
    }

    // AMBIL DATA
    $readings = $query->orderBy('tanggal', 'desc')->get();
    
    // KELOMPOKKAN
    // Pastikan string 'nama_lokasi' ini SESUAI dengan nama kolom di database kamu
    $groupedReadings = $readings->groupBy('nama_lokasi');

    return view('admin.readings-air', compact('readings', 'groupedReadings'));
}

public function readingsListrik(Request $request)
{
    // ... (Logika filter kamu biarkan saja) ...
    $query = \App\Models\MeterListrik::query();
    
if ($request->filled('lokasi')) {
        // Coba pakai 'nama_lokasi' kalau kolom di DB namanya itu
        // Jika kolom di DB beneran 'lokasi', ubah kembali jadi 'lokasi'
        $query->where('lokasi', $request->lokasi); 
    }

    // 2. FILTER STATUS METER
    if ($request->filled('status_meter')) {
        $query->where('status_meter', $request->status_meter);
    }

    // 3. FILTER TANGGAL MULAI
    if ($request->filled('tanggal_mulai')) {
        $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
    }

    // 4. FILTER TANGGAL SELESAI
    if ($request->filled('tanggal_selesai')) {
        $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
    }

    // 5. FILTER PETUGAS
    if ($request->filled('petugas')) {
        $query->where('petugas', 'like', '%' . $request->petugas . '%');
    }

    // AMBIL DATA
    $readings = $query->orderBy('tanggal', 'desc')->get();
    
    // KELOMPOKKAN
    // Pastikan string 'nama_lokasi' ini SESUAI dengan nama kolom di database kamu
    $groupedReadings = $readings->groupBy('nama_lokasi');
    
    return view('admin.readings-listrik', compact('readings', 'groupedReadings'));
}

public function readingsGabungan(Request $request)
{
    $query = \App\Models\MeterReading::query(); // (Sesuaikan query filter kamu)
    
    // 1. Ambil data asli (Ini yang dicari sama view kamu yang lama)
    $readings = $query->orderBy('tanggal', 'desc')->get();
    
    // 2. Buat data yang sudah dikelompokkan
    $groupedReadings = $readings->groupBy('nama_lokasi');
    
    // 3. KIRIM KEDUANYA KE VIEW (Perhatikan compact-nya)
    return view('admin.readings-gabungan', compact('readings', 'groupedReadings'));
}

    public function destroyAir($id)
    {
        $reading = MeterAir::findOrFail($id);
        
        // Hapus foto jika ada
        if ($reading->foto) {
            \Storage::disk('public')->delete($reading->foto);
        }
        
        $reading->delete();
        
        return redirect()->back()->with('success', 'Data air berhasil dihapus');
    }

    public function destroyListrik($id)
    {
        $reading = MeterListrik::findOrFail($id);
        
        if ($reading->foto) {
            \Storage::disk('public')->delete($reading->foto);
        }
        
        $reading->delete();
        
        return redirect()->back()->with('success', 'Data listrik berhasil dihapus');
    }

    public function readings()
{
    return redirect()->route('admin.readings.gabungan');
}

/**
 * Destroy lama (jika masih diperlukan)
 */
public function destroy($id)
{
    // Coba cari di tabel air dulu
    $air = MeterAir::find($id);
    if ($air) {
        return $this->destroyAir($id);
    }
    
    // Coba cari di tabel listrik
    $listrik = MeterListrik::find($id);
    if ($listrik) {
        return $this->destroyListrik($id);
    }
    
    return redirect()->back()->with('error', 'Data tidak ditemukan');
}
}