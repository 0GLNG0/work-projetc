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
        // Filter Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // PASTIKAN FILTER BULAN INI ADA DI FUNGSI LISTRIK!
        if ($request->filled('bulan')) {
            $pecah = explode('-', $request->bulan);
            if (count($pecah) == 2) {
                // Di Listrik, panggilnya langsung 'tanggal' (nggak usah 'a.tanggal')
                $query->whereYear('tanggal', $pecah[0])
                      ->whereMonth('tanggal', $pecah[1]);
            }
        }
        // AMBIL DATA & KELOMPOKKAN
        $readings = $query->orderBy('tanggal', 'asc')->get();
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
        // Filter Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // PASTIKAN FILTER BULAN INI ADA DI FUNGSI LISTRIK!
        if ($request->filled('bulan')) {
            $pecah = explode('-', $request->bulan);
            if (count($pecah) == 2) {
                // Di Listrik, panggilnya langsung 'tanggal' (nggak usah 'a.tanggal')
                $query->whereYear('tanggal', $pecah[0])
                      ->whereMonth('tanggal', $pecah[1]);
            }
        }

        $readings = $query->orderBy('tanggal', 'asc')->get();
        $groupedReadings = $readings->groupBy('lokasi'); 
        
        $daftarLokasi = \App\Models\MeterListrik::select('lokasi')->whereNotNull('lokasi')->distinct()->pluck('lokasi');

        return view('admin.readings-listrik', compact('readings', 'groupedReadings', 'lokasiAktif', 'daftarLokasi'));
    }

public function readingsGabungan(Request $request)
    {
        $lokasiAktif = $request->lokasi;

        // =======================================================
        // 1. DATA STATISTIK UNTUK CARD DI ATAS TABEL
        // =======================================================
        $totalAir = \App\Models\MeterAir::count();
        $totalListrik = \App\Models\MeterListrik::count();
        $totalPemakaianAir = \App\Models\MeterAir::sum('pemakaian');
        $totalPemakaianListrik = \App\Models\MeterListrik::sum('pemakaian');
        $todayAir = \App\Models\MeterAir::whereDate('tanggal', today())->count();
        $todayListrik = \App\Models\MeterListrik::whereDate('tanggal', today())->count();

        // =======================================================
        // 2. QUERY UTAMA: GABUNGKAN TABEL AIR DAN LISTRIK
        // =======================================================
        $query = \Illuminate\Support\Facades\DB::table('meter_air_readings as a')
            ->select(
                'a.id', 'a.tanggal', 'a.lokasi', 'a.petugas',
                'a.meter_awal as meter_awal_air',
                'a.meter_akhir as meter_akhir_air',
                'a.pemakaian as pemakaian_air',
                'a.meter_akhir as meter_pompa', 
                'a.pemakaian as hasil_m3',
                
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

        // =======================================================
        // 3. PASUKAN FILTER
        // =======================================================
        
        // A. Filter Lokasi
        if ($lokasiAktif) {
            $query->where('a.lokasi', $lokasiAktif);
        }

        // B. Filter Tanggal Mulai & Selesai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('a.tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('a.tanggal', '<=', $request->tanggal_selesai);
        }

        // C. Filter Bulan (Sudah Diperbaiki!)
        if ($request->filled('bulan')) {
            $pecah = explode('-', $request->bulan);
            if (count($pecah) == 2) {
                $tahun = $pecah[0];
                $bulan = $pecah[1];
                
                // PENTING: Harus pakai a.tanggal biar database nggak bingung
                $query->whereYear('a.tanggal', $tahun)
                      ->whereMonth('a.tanggal', $bulan);
            }
        }

        // =======================================================
        // 4. AMBIL DATA DAN KELOMPOKKAN
        // =======================================================
        $readings = $query->orderBy('a.tanggal', 'asc')->get();
        $groupedReadings = collect($readings)->groupBy('lokasi');
        
        // =======================================================
        // 5. AMBIL DAFTAR LOKASI BUAT TOMBOL FILTER
        // =======================================================
        $daftarLokasi = \Illuminate\Support\Facades\DB::table('meter_air_readings')
            ->whereNotNull('lokasi')->distinct()->pluck('lokasi');

        // =======================================================
        // 6. LEMPAR KE HALAMAN TAMPILAN (Paling Bawah!)
        // =======================================================
        return view('admin.readings-gabungan', compact(
            'readings', 'groupedReadings', 'lokasiAktif', 'daftarLokasi', 
            'totalAir', 'totalListrik', 'totalPemakaianAir', 'totalPemakaianListrik', 
            'todayAir', 'todayListrik'
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
    public function exportAir(Request $request)
    {
        $lokasi = $request->lokasi;
        $namaFile = 'Laporan_Meter_Air_' . ($lokasi ?? 'Semua_Lokasi') . '_' . date('d-m-Y') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExportAir($lokasi), $namaFile);
    }

    public function exportListrik(Request $request)
    {
        $lokasi = $request->lokasi;
        $namaFile = 'Laporan_Meter_Listrik_' . ($lokasi ?? 'Semua_Lokasi') . '_' . date('d-m-Y') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExportListrik($lokasi), $namaFile);
    }
    public function exportBulanan(Request $request)
    {
        $lokasi = $request->lokasi;
        $namaFile = 'Rekap_Bulanan_' . ($lokasi ?? 'Semua_Lokasi') . '_' . date('d-m-Y') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExportBulanan($lokasi), $namaFile);
    }

    public function importAir(Request $request)
    {
        // 1. Validasi pastikan yang diupload beneran Excel
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        // 2. Proses sedot data pakai class Import yang barusan kita buat!
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ImportMeterAir, $request->file('file_excel'));

        // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', '✨ Ribuan Data Air sukses bersinar masuk ke Database!');
    }
    public function importListrik(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ImportMeterListrik, $request->file('file_excel'));

        return redirect()->back()->with('success', '⚡ Ribuan Data Listrik sukses tersetrum masuk ke Database!');
    }
}