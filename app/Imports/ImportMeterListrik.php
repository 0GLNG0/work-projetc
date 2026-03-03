<?php

namespace App\Imports;

use App\Models\MeterListrik;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class ImportMeterListrik implements ToModel, WithStartRow, WithCalculatedFormulas
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
    // 1. CEK TANGGAL DI INDEX 0 (Kolom A)
    if (!isset($row[0]) || $row[0] == '') return null;

    // 2. SIHIR PENERJEMAH TANGGAL (Index 0)
    $tgl_excel = $row[0];
    try {
        if (is_numeric($tgl_excel)) {
            $tanggal_fix = Date::excelToDateTimeObject($tgl_excel)->format('Y-m-d');
        } else {
            $tanggal_fix = Carbon::createFromFormat('d/m/Y', $tgl_excel)->format('Y-m-d');
        }
    } catch (\Exception $e) {
        $tanggal_fix = now()->format('Y-m-d');
    }

    // 3. SIHIR PENERJEMAH JAM (Index 1)
    $jam_excel = $row[1] ?? '00:00:00';
    try {
        if (is_numeric($jam_excel)) {
            $jam_fix = Date::excelToDateTimeObject($jam_excel)->format('H:i:s');
        } else {
            $jam_fix = $jam_excel;
        }
    } catch (\Exception $e) {
        $jam_fix = '00:00:00';
    }

    // 4. MAPPING DATA SESUAI GAMBAR (A=0, B=1, dst)
    $lokasi      = $row[2] ?? '-';
    $petugas     = $row[3] ?? 'Import System';
    
    // Meter Utama (KWH)
    $meter_akhir = $this->bersihkanAngka($row[4] ?? 0);
    $pemakaian   = $this->bersihkanAngka($row[5] ?? 0);
    $meter_awal  = $meter_akhir - $pemakaian;

    // LWBP
    $lwbp_akhir     = $this->bersihkanAngka($row[6] ?? 0);
    $pemakaian_lwbp = $this->bersihkanAngka($row[7] ?? 0);
    $lwbp_awal      = $lwbp_akhir - $pemakaian_lwbp;

    // WBP
    $wbp_akhir      = $this->bersihkanAngka($row[8] ?? 0);
    $pemakaian_wbp  = $this->bersihkanAngka($row[9] ?? 0);
    $wbp_awal       = $wbp_akhir - $pemakaian_wbp;

    // KVARH
    $kvarh_akhir     = $this->bersihkanAngka($row[10] ?? 0);
    $pemakaian_kvarh = $this->bersihkanAngka($row[11] ?? 0);
    $kvarh_awal      = $kvarh_akhir - $pemakaian_kvarh;

    return new MeterListrik([
        'tanggal'         => $tanggal_fix,
        'jam'             => $jam_fix,
        'lokasi'          => $lokasi,
        'petugas'         => $petugas,
        
        'meter_awal'      => $meter_awal,
        'meter_akhir'     => $meter_akhir,
        'pemakaian'       => $pemakaian,
        
        'lwbp_awal'       => $lwbp_awal,
        'lwbp_akhir'      => $lwbp_akhir,
        'pemakaian_lwbp'  => $pemakaian_lwbp,
        
        'wbp_awal'        => $wbp_awal,
        'wbp_akhir'       => $wbp_akhir,
        'pemakaian_wbp'   => $pemakaian_wbp,
        
        'kvarh_awal'      => $kvarh_awal,
        'kvarh_akhir'     => $kvarh_akhir,
        'pemakaian_kvarh' => $pemakaian_kvarh,
        
        'status_meter'    => 'normal',
    ]);
}
}