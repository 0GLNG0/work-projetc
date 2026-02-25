<?php

namespace App\Exports;

use App\Models\MeterReading;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonthlyReportExport implements WithMultipleSheets
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Sheet Rekapitulasi
        $sheets[] = new RekapBulananSheet($this->bulan, $this->tahun);

        // Sheet per Lokasi
        $lokasiList = array_keys(MeterReading::$lokasiOptions);
        foreach ($lokasiList as $lokasi) {
            $sheets[] = new LokasiSheet($lokasi, $this->bulan, $this->tahun);
        }

        return $sheets;
    }
}