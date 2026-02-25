<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MeterListrikExport implements WithMultipleSheets
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
            $sheets[] = new MeterListrikSheet($namaLokasi, $data);
        }

        return $sheets;
    }
}