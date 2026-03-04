<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportBulanan implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $tahun;

    public function __construct($tahun = null)
    {
        // Kita pakai filter tahun sekarang karena mau lihat rekap 1 tahun
        $this->tahun = $tahun ?? date('Y');
    }

public function collection()
{
    // 1. Ambil SEMUA data tanpa filter tahun
    $data = DB::table('meter_air_readings')
        ->select('lokasi', 'tanggal', 'pemakaian')
        ->get();

    // 2. Ambil daftar Tahun unik dan daftar Lokasi unik
    $daftarTahun = $data->map(function($item) {
        return \Carbon\Carbon::parse($item->tanggal)->year;
    })->unique()->sortDesc(); // Tahun terbaru di atas

    $daftarLokasi = $data->pluck('lokasi')->unique()->sort();

    $rekap = collect();

    foreach ($daftarTahun as $tahun) {
        foreach ($daftarLokasi as $lokasi) {
            $row = [
                'tahun'  => $tahun,
                'lokasi' => $lokasi
            ];
            $totalTahunan = 0;

            // 3. Looping bulan 1-12 untuk tahun & lokasi tersebut
            for ($m = 1; $m <= 12; $m++) {
                $nilaiBulan = $data->where('lokasi', $lokasi)
                    ->filter(function($item) use ($m, $tahun) {
                        $dt = \Carbon\Carbon::parse($item->tanggal);
                        return $dt->month == $m && $dt->year == $tahun;
                    })
                    ->sum('pemakaian');

                $row[$m] = $nilaiBulan > 0 ? $nilaiBulan : 0;
                $totalTahunan += $nilaiBulan;
            }

            // Tambahkan baris jika ada pemakaian di tahun tersebut
            if ($totalTahunan > 0) {
                $row['total'] = $totalTahunan;
                $rekap->push($row);
            }
        }
    }

    return $rekap;
}
    public function headings(): array
{
    return [
        ['LAPORAN REKAPITULASI PEMAKAIAN SEMUA TAHUN'],
        [], 
        [
            'TAHUN',           // Kolom baru
            'LOKASI / POMPA', 
            'JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 
            'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES', 
            'TOTAL 1 TAHUN'
        ]
    ];
}

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestCol = 'O'; // Kolom terakhir (Total)

                // Judul Tengah
                $sheet->mergeCells("A1:O1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Style Header Tabel (Baris 3)
                $sheet->getStyle("A3:{$highestCol}3")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE5E7EB']]
                ]);

                // Border Seluruh Data
                $sheet->getStyle("A3:{$highestCol}{$highestRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Warnai Kolom Total (N) jadi Kuning biar menonjol
                $sheet->getStyle("O3:O{$highestRow}")->applyFromArray([
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFF00']],
    'font' => ['bold' => true]
]);
            },
        ];
    }
}