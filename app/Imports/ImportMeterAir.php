<?php

namespace App\Imports;

use App\Models\MeterAir;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;

class ImportMeterAir implements ToModel, WithStartRow, WithCalculatedFormulas
{
    public function startRow(): int { return 2; }

    // Senjata rahasia pembersih angka Excel
    private function bersihkanAngka($angka) {
        if (empty($angka)) return 0;
        // Ubah koma jadi titik, lalu hapus semua huruf/spasi
        $bersih = preg_replace('/[^0-9.-]/', '', str_replace(',', '.', $angka));
        return (float) $bersih;
    }

    public function model(array $row)
    {
        if (!isset($row[2]) || $row[2] == '') return null;

        $tgl_excel = $row[2];
        if (is_numeric($tgl_excel)) {
            $tanggal_fix = Date::excelToDateTimeObject($tgl_excel)->format('Y-m-d');
        } else {
            $tanggal_fix = Carbon::createFromFormat('d/m/Y', $tgl_excel)->format('Y-m-d');
        }

        // Tembak pakai fungsi pembersih angka
        $meter_akhir = $this->bersihkanAngka($row[4] ?? 0);
        $pemakaian   = $this->bersihkanAngka($row[5] ?? 0);
        $meter_awal  = $meter_akhir - $pemakaian;

        return new MeterAir([
            'tanggal'     => $tanggal_fix,
            'jam'         => $row[3] ?? '00:00:00',
            'lokasi'      => $row[1] ?? '-',
            'petugas'     => 'Import System',
            'meter_awal'  => $meter_awal,
            'meter_akhir' => $meter_akhir,
            'pemakaian'   => $pemakaian,
        ]);
    }
}