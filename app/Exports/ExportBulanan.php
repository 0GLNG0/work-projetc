<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportBulanan implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $lokasi;

    public function __construct($lokasi = null)
    {
        $this->lokasi = $lokasi;
    }

    public function collection()
    {
        // 1. Tarik semua data Gabungan Air & Listrik
        $query = DB::table('meter_air_readings as a')
            ->select('a.tanggal', 'a.lokasi', 'a.pemakaian as pemakaian_air', 'l.pemakaian as pemakaian_listrik')
            ->leftJoin('meter_listrik_readings as l', function($join) {
                $join->on('a.tanggal', '=', 'l.tanggal')
                     ->on('a.lokasi', '=', 'l.lokasi');
            });

        if ($this->lokasi) {
            $query->where('a.lokasi', $this->lokasi);
        }

        $data = $query->get();

        // 2. Olah dan jumlahkan datanya per Bulan & Lokasi (Pakai Collection biar aman di Windows Server / SQL)
        $rekap = [];
        foreach ($data as $row) {
            $bulanTahun = date('F Y', strtotime($row->tanggal)); // Contoh: February 2026
            $key = $bulanTahun . '_' . $row->lokasi;

            if (!isset($rekap[$key])) {
                $rekap[$key] = (object) [
                    'bulan' => $bulanTahun,
                    'lokasi' => $row->lokasi,
                    'total_air' => 0,
                    'total_listrik' => 0
                ];
            }

            // Tambahkan terus pemakaiannya ke bulan yang sama
            $rekap[$key]->total_air += $row->pemakaian_air;
            $rekap[$key]->total_listrik += $row->pemakaian_listrik;
        }

        // Kembalikan dalam bentuk Collection
        return collect(array_values($rekap));
    }

    public function headings(): array
    {
        $judul = $this->lokasi ? strtoupper($this->lokasi) : 'SEMUA LOKASI';
        return [
            ['REKAPITULASI PEMAKAIAN BULANAN - ' . $judul],
            [], // Baris kosong
            ['PERIODE BULAN', 'LOKASI', 'TOTAL PEMAKAIAN AIR (M3)', 'TOTAL PEMAKAIAN LISTRIK (KWH)']
        ];
    }

    public function map($row): array
    {
        return [
            strtoupper($row->bulan),
            $row->lokasi,
            $row->total_air,
            $row->total_listrik
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Judul
                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Header Tabel
                $sheet->getStyle('A3:D3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE5E7EB']]
                ]);

                // Border & Alignment seluruh tabel
                $sheet->getStyle('A3:D' . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
            },
        ];
    }
}