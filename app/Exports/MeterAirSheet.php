<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MeterAirSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    private $lokasi;
    private $readings;

    public function __construct($lokasi, $readings)
    {
        $this->lokasi = $lokasi;
        $this->readings = $readings;
    }

    public function collection()
    {
        return $this->readings;
    }

    public function title(): string
    {
        // Nama tab di Excel (Max 31 karakter)
        return substr($this->lokasi, 0, 31);
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Lokasi',
            'Meter Awal',
            'Meter Akhir',
            'Pemakaian (m³)',
            'Status',
            'Petugas'
        ];
    }

    public function map($reading): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $reading->tanggal->format('d/m/Y'),
            $reading->nama_lokasi,
            $reading->meter_awal,
            $reading->meter_akhir,
            $reading->pemakaian,
            $reading->status_meter,
            $reading->petugas
        ];
    }
}