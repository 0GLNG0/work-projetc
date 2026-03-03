<?php

namespace App\Imports;

use App\Models\MeterAir;
use App\Models\Lokasi; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class ImportMeterAir implements ToModel, WithStartRow, WithCalculatedFormulas
{
    public function startRow(): int { return 2; }

    // Senjata rahasia pembersih angka Excel
    private function bersihkanAngka($angka) {
        if (empty($angka)) return 0;
        $bersih = preg_replace('/[^0-9.-]/', '', str_replace(',', '.', $angka));
        return (float) $bersih;
    }

    public function model(array $row)
    {
        // Cek apakah baris kosong (Cek dari kolom Tanggal yaitu index 2, BUKAN index 0)
        if (!isset($row[2]) || $row[2] == '') return null;

        // 1. Ambil data mentah sesuai hasil CCTV array barusan!
        $namaLokasi = trim($row[1] ?? ''); // Index 1: Wilis Utara
        
        // Bersihkan angka meteran
        $meter_akhir = $this->bersihkanAngka($row[4] ?? 0); // Index 4
        $pemakaian   = $this->bersihkanAngka($row[5] ?? 0); // Index 5
        
        $petugas = $row[6] ?? 'Petugas PDAM'; // Index 6 (kasih default kalau kosong)

        // 2. Perbaiki Format Tanggal (Serial Excel 41639 -> 2013-12-31)
        try {
            $tanggal_fix = Date::excelToDateTimeObject($row[2])->format('Y-m-d');
        } catch (\Exception $e) {
            $tanggal_fix = now()->format('Y-m-d');
        }

        // 3. Perbaiki Format Jam (Serial Excel 0.375 -> 09:00:00)
        try {
            $jam_fix = Date::excelToDateTimeObject($row[3])->format('H:i:s');
        } catch (\Exception $e) {
            $jam_fix = '00:00:00';
        }

        // 4. Tarik Waktu Aktif dari Database untuk Rumus
        $lokasiInfo = Lokasi::where('nama_lokasi', $namaLokasi)->first();
        $waktuAktif = $lokasiInfo ? $lokasiInfo->waktu_aktif_pompa : 24;

        // 5. Eksekusi Rumus Mutlak: (Pemakaian / (3600 * Jam Aktif)) * 100
        $literPerDetik = 0;
        if ($waktuAktif > 0 && $pemakaian > 0) {
            $literPerDetik = round(($pemakaian / (3600 * $waktuAktif)) * 100, 2);
        }

        // 6. Masukkan ke Database!
        return new MeterAir([
            'tanggal'         => $tanggal_fix,
            'jam'             => $jam_fix,
            'lokasi'          => $namaLokasi,
            'petugas'         => $petugas,
            'meter_akhir'     => $meter_akhir,
            'pemakaian'       => $pemakaian,
            'liter_per_detik' => $literPerDetik, 
        ]);
    }
}