<?php

namespace App\Imports;

use App\Models\MeterAir; 
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class ReadingsImport implements ToModel, WithStartRow
{
    /**
     * Kita mulai baca dari BARIS 7 (data pertama tanggal 1)
     * karena baris 1-6 adalah header dan baris saldo awal.
     */
    public function startRow(): int
    {
        return 7;
    }

    public function model(array $row)
    {
        // $row[4] adalah kolom E (TGL)
        // Jika kolom TGL kosong, jangan diimport
        if (!isset($row[4]) || empty($row[4])) {
            return null;
        }

        return new MeterAir([
            'lokasi'            => 'Kuwak', // Sesuaikan lokasinya
            'tanggal'           => $this->transformDate($row[0]),
            'jam'              => now()->format('H:i:s'), // Bisa disesuaikan jika ada kolom jam di Excel
            
            // MAPPING SESUAI KOLOM EXCEL (Mulai dari Index 0)
            'meter_air'         => $this->cleanNumber($row[1]),  // Kolom F (METER POMPA I)
            'pemakaian_air'     => $this->cleanNumber($row[2]),  // Kolom G (HASIL M3)
            
            'meter_listrik'     => $this->cleanNumber($row[13]), // Kolom S (KWH)
            'pemakaian_listrik' => $this->cleanNumber($row[14]), // Kolom T (HASIL Listrik)
            
            'petugas'           => 'Import Excel Januari',
            'status_meter'      => 'Normal'
        ]);
    }

    private function cleanNumber($value) {
        // Hilangkan titik ribuan agar jadi angka murni (134.894 -> 134894)
        return str_replace('.', '', $value);
    }

    private function transformDate($value) {
        // Karena di Excel cuma angka "1", "2", dst (hari), kita gabungkan dengan bulan & tahun
        $bulanTahun = "2026-01-"; // Sesuaikan dengan judul "Januari" di Excelmu
        $hari = str_pad($value, 2, '0', STR_PAD_LEFT);
        return $bulanTahun . $hari;
    }
}