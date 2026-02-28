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
        // TANGGAL SEKARANG ADA DI INDEX 3 (Kolom D)
        if (!isset($row[3]) || $row[3] == '') return null;

        // 1. SIHIR PENERJEMAH TANGGAL (Index 3)
        $tgl_excel = $row[3];
        if (is_numeric($tgl_excel)) {
            $tanggal_fix = Date::excelToDateTimeObject($tgl_excel)->format('Y-m-d');
        } else {
            $tanggal_fix = Carbon::createFromFormat('d/m/Y', $tgl_excel)->format('Y-m-d');
        }

        // 2. SIHIR PENERJEMAH JAM (Index 4 / Kolom E)
        $jam_excel = $row[4] ?? '00:00:00';
        if (is_numeric($jam_excel)) {
            $jam_fix = Date::excelToDateTimeObject($jam_excel)->format('H:i:s');
        } else {
            $jam_fix = $jam_excel;
        }

        // MATEMATIKA DIMULAI DARI INDEX 5
        $lwbp_akhir      = $this->bersihkanAngka($row[5] ?? 0);
        $pemakaian_lwbp  = $this->bersihkanAngka($row[6] ?? 0);
        $lwbp_awal       = $lwbp_akhir - $pemakaian_lwbp;

        $wbp_akhir       = $this->bersihkanAngka($row[7] ?? 0);
        $pemakaian_wbp   = $this->bersihkanAngka($row[8] ?? 0);
        $wbp_awal        = $wbp_akhir - $pemakaian_wbp;

        $kvarh_akhir     = $this->bersihkanAngka($row[9] ?? 0);
        $pemakaian_kvarh = $this->bersihkanAngka($row[10] ?? 0);
        $kvarh_awal      = $kvarh_akhir - $pemakaian_kvarh;

        $meter_akhir     = $this->bersihkanAngka($row[11] ?? 0);
        $pemakaian_total = $this->bersihkanAngka($row[12] ?? 0);
        $meter_awal      = $meter_akhir - $pemakaian_total;

        return new MeterListrik([
            'tanggal'           => $tanggal_fix,
            'jam'               => $jam_fix,
            'lokasi'            => $row[1] ?? '-', // Index 1
            'petugas'           => 'Import System',
            
            'lwbp_awal'         => $lwbp_awal,
            'lwbp_akhir'        => $lwbp_akhir,
            'pemakaian_lwbp'    => $pemakaian_lwbp,
            
            'wbp_awal'          => $wbp_awal,
            'wbp_akhir'         => $wbp_akhir,
            'pemakaian_wbp'     => $pemakaian_wbp,
            
            'kvarh_awal'        => $kvarh_awal,
            'kvarh_akhir'       => $kvarh_akhir,
            'pemakaian_kvarh'   => $pemakaian_kvarh,
            
            'meter_awal'        => $meter_awal,
            'meter_akhir'       => $meter_akhir,
            'pemakaian'         => $pemakaian_total,
        ]);
    }
}