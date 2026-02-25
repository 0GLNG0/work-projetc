<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MeterAirExport implements WithMultipleSheets
{
    protected $readings;

    public function __construct($readings)
    {
        $this->readings = $readings;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Kelompokkan data berdasarkan nama_lokasi
        $grouped = $this->readings->groupBy('nama_lokasi');

        foreach ($grouped as $namaLokasi => $data) {
            $sheets[] = new MeterAirSheet($namaLokasi, $data);
        }

        return $sheets;
    }
}