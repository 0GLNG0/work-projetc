<?php

namespace App\Http\Controllers;

use App\Models\MeterAir;
use App\Models\MeterListrik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\ReadingsImport;

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

        // Tangkap lokasi yang sedang diklik dari URL
        $lokasiAktif = $request->lokasi;

        // 1. FILTER LOKASI
        if ($lokasiAktif) {
            $query->where('lokasi', $lokasiAktif); 
        }

        // 2. FILTER LAINNYA
        if ($request->filled('status_meter')) {
            $query->where('status_meter', $request->status_meter);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }
        if ($request->filled('petugas')) {
            $query->where('petugas', 'like', '%' . $request->petugas . '%');
        }

        // AMBIL DATA & KELOMPOKKAN
        $readings = $query->orderBy('tanggal', 'desc')->get();
        $groupedReadings = $readings->groupBy('lokasi'); // Ubah ke 'lokasi' biar seragam
        
        // AMBIL DAFTAR LOKASI UNTUK TOMBOL (Otomatis dari database)
        $daftarLokasi = \App\Models\MeterAir::select('lokasi')->whereNotNull('lokasi')->distinct()->pluck('lokasi');

        // KIRIM KE VIEW (Tambah lokasiAktif dan daftarLokasi)
        return view('admin.readings-air', compact('readings', 'groupedReadings', 'lokasiAktif', 'daftarLokasi'));
    }

    public function readingsListrik(Request $request)
    {
        $query = \App\Models\MeterListrik::query();
        
        $lokasiAktif = $request->lokasi;

        if ($lokasiAktif) {
            $query->where('lokasi', $lokasiAktif); 
        }

        if ($request->filled('status_meter')) $query->where('status_meter', $request->status_meter);
        if ($request->filled('tanggal_mulai')) $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        if ($request->filled('tanggal_selesai')) $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        if ($request->filled('petugas')) $query->where('petugas', 'like', '%' . $request->petugas . '%');

        $readings = $query->orderBy('tanggal', 'desc')->get();
        $groupedReadings = $readings->groupBy('lokasi'); 
        
        $daftarLokasi = \App\Models\MeterListrik::select('lokasi')->whereNotNull('lokasi')->distinct()->pluck('lokasi');

        return view('admin.readings-listrik', compact('readings', 'groupedReadings', 'lokasiAktif', 'daftarLokasi'));
    }

    public function readingsGabungan(Request $request)
    {
        $totalAir = \App\Models\MeterAir::count();
        $totalListrik = \App\Models\MeterListrik::count();
        $todayAir = \App\Models\MeterAir::whereDate('tanggal', today())->count();
        $todayListrik = \App\Models\MeterListrik::whereDate('tanggal', today())->count();
        $lokasiAktif = $request->lokasi;

        // 1. KITA JOIN TABEL AIR DAN LISTRIK (Persis kayak rumus Excel semalam)
        // PASTIKAN NAMA TABELNYA SESUAI ('meter_air_readings' & 'meter_listrik_readings')
$query = \Illuminate\Support\Facades\DB::table('meter_air_readings as a')
            ->select(
                'a.id', 'a.tanggal', 'a.lokasi', 'a.petugas',
                
                // --- KEMBALIKAN NAMA ASLI KOLOM AIR ---
                'a.meter_awal as meter_awal_air',
                'a.meter_akhir as meter_akhir_air',
                'a.pemakaian as pemakaian_air', // <--- INI DIA PELAKUNYA!
                
                // --- ALIAS BUAT EXCEL/PDF JAGA-JAGA ---
                'a.meter_akhir as meter_pompa', 
                'a.pemakaian as hasil_m3',
                
                // --- KEMBALIKAN KOLOM LISTRIK LENGKAP ---
                'l.lwbp_awal', 'l.lwbp_akhir', 'l.pemakaian_lwbp',
                'l.wbp_awal', 'l.wbp_akhir', 'l.pemakaian_wbp',
                'l.kvarh_awal', 'l.kvarh_akhir', 'l.pemakaian_kvarh',
                'l.meter_awal as meter_awal_listrik',
                'l.meter_akhir as meter_akhir_listrik',
                'l.pemakaian as pemakaian_listrik',
                'l.meter_akhir as kwh', 
                'l.pemakaian as hasil_kwh'
            )
            ->leftJoin('meter_listrik_readings as l', function($join) {
                $join->on('a.tanggal', '=', 'l.tanggal')
                     ->on('a.lokasi', '=', 'l.lokasi');
            });

        // 2. FILTER LOKASI DARI TOMBOL
        if ($lokasiAktif) {
            $query->where('a.lokasi', $lokasiAktif);
        }

        // (Opsional) Filter tanggal kalau kamu masih pakai
        if ($request->filled('tanggal_mulai')) $query->whereDate('a.tanggal', '>=', $request->tanggal_mulai);
        if ($request->filled('tanggal_selesai')) $query->whereDate('a.tanggal', '<=', $request->tanggal_selesai);

        // 3. AMBIL DATA
        $readings = $query->orderBy('a.tanggal', 'desc')->get();
        
        // 4. KELOMPOKKAN BERDASARKAN LOKASI
        // Pakai collect() karena ini data dari DB Builder, bukan dari Eloquent Model
        $groupedReadings = collect($readings)->groupBy('lokasi');
        
        // 5. AMBIL DAFTAR LOKASI BUAT TOMBOL FILTER
        $daftarLokasi = \Illuminate\Support\Facades\DB::table('meter_air_readings')
                        ->whereNotNull('lokasi')->distinct()->pluck('lokasi');

        return view('admin.readings-gabungan', compact(
            'readings', 'groupedReadings', 'lokasiAktif', 'daftarLokasi', 
            'totalAir', 'totalListrik', 'todayAir', 'todayListrik'
        ));
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

public function importExcel(Request $request)
{
    $request->validate([
        'file_excel' => 'required|mimes:xlsx,xls'
    ]);

    // Menjalankan proses import
    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ReadingsImport, $request->file('file_excel'));
    
    return redirect()->back()->with('success', 'Data Excel Januari berhasil diimport!');
}
public function exportExcel(Request $request)
{
    $lokasi = $request->query('lokasi'); // Bisa export semua atau per lokasi
    
    $namaFile = 'Laporan_Pompa_Semua_Lokasi_' . date('d-M-Y') . '.xlsx';
    
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LaporanExport(), $namaFile);
}

public function destroyGabungan($id)
    {
        // 1. Kita cari data pakai jalur bawah (DB::table) biar dapet fisik aslinya
        $air = \Illuminate\Support\Facades\DB::table('meter_air_readings')->where('id', $id)->first();
        
        if ($air) {
            // 2. Cari data Listrik pasangannya
            $listrik = \Illuminate\Support\Facades\DB::table('meter_listrik_readings')
                        ->where('tanggal', $air->tanggal)
                        ->where('lokasi', $air->lokasi)
                        ->first();
            
            // 3. Hapus foto listrik (kalau ada) & HAPUS FISIK LISTRIK
            if ($listrik) {
                if ($listrik->foto) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($listrik->foto);
                }
                // Musnahkan listrik permanen dari database
                \Illuminate\Support\Facades\DB::table('meter_listrik_readings')
                    ->where('tanggal', $air->tanggal)
                    ->where('lokasi', $air->lokasi)
                    ->delete();
            }

            // 4. Hapus foto air (kalau ada) & HAPUS FISIK AIR
            if ($air->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($air->foto);
            }
            // Musnahkan air permanen dari database
            \Illuminate\Support\Facades\DB::table('meter_air_readings')->where('id', $id)->delete();

            return redirect()->back()->with('success', '✅ Data Air dan Listrik sukses dimusnahkan permanen sampai ke akar!');
        }

        return redirect()->back()->with('error', '❌ Data tidak ditemukan!');
    }
}