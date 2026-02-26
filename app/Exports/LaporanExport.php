<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// =========================================================================
// 1. CLASS UTAMA (BUKU EXCEL-NYA)
// Tugasnya cuma nyari ada lokasi apa aja, terus bikin Sheet buat tiap lokasi
// =========================================================================
class LaporanExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $sheets = [];
        
        // Ambil semua daftar lokasi unik dari database (misal dari tabel air)
        $daftarLokasi = DB::table('meter_air_readings')
                        ->whereNotNull('lokasi')
                        ->distinct()
                        ->pluck('lokasi');

        // Bikin lembaran (sheet) baru untuk setiap lokasi yang ketemu
        foreach ($daftarLokasi as $lokasi) {
            $sheets[] = new LaporanPerLokasiSheet($lokasi);
        }

        return $sheets;
    }
}

// =========================================================================
// 2. CLASS LEMBARAN (SHEET-NYA)
// =========================================================================
class LaporanPerLokasiSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    private $lokasi;

    public function __construct($lokasi)
    {
        $this->lokasi = $lokasi;
    }

    public function title(): string
    {
        return $this->lokasi; 
    }

    public function collection()
    {
        // Sesuaikan 'meter_air_readings' dengan nama tabel aslimu ya!
        return DB::table('meter_air_readings as a')
            ->leftJoin('meter_listrik_readings as l', function($join) {
                $join->on('a.tanggal', '=', 'l.tanggal')
                     ->on('a.lokasi', '=', 'l.lokasi');
            })
            ->where('a.lokasi', $this->lokasi)
            ->orderBy('a.tanggal', 'asc')
            ->get([
                'a.tanggal',
                'a.meter_akhir as meter_pompa',
                'a.pemakaian as hasil_m3',
                'l.lwbp_akhir', 'l.pemakaian_lwbp',
                'l.wbp_akhir', 'l.pemakaian_wbp',
                'l.kvarh_akhir', 'l.pemakaian_kvarh',
                'l.meter_akhir as kwh', 'l.pemakaian as hasil_kwh'
            ]);
    }

    public function headings(): array
    {
        return [
            // Baris 1: Judul Utama
            ['LAPORAN REKAPITULASI POMPA - LOKASI: ' . strtoupper($this->lokasi)],
            // Baris 2: Kosong (biar ada jarak)
            [], 
            // Baris 3: Header Tabel
            [
                'TGL', 'METER POMPA', 'HASIL M3', 'LTR/DTK', 
                'L W B P', 'HASIL', 'W B P', 'HASIL', 
                'K V A R H', 'HASIL', 'K W H', 'HASIL'
            ]
        ];
    }

    public function map($row): array
    {
        $liter_per_detik = 0;
        if ($row->hasil_m3 > 0) {
            $liter_per_detik = round(($row->hasil_m3 * 1000) / 86400, 2);
        }
        return [
            date('d-M-y', strtotime($row->tanggal)), 
            $row->meter_pompa,                       
            $row->hasil_m3,                          
            $liter_per_detik,                        
            $row->lwbp_akhir,                        
            $row->pemakaian_lwbp,                    
            $row->wbp_akhir,                         
            $row->pemakaian_wbp,                     
            $row->kvarh_akhir,                       
            $row->pemakaian_kvarh,                   
            $row->kwh,                               
            $row->hasil_kwh,                         
        ];
    }

    // 🔥 INI JURUS SAKTI BUAT BIKIN BORDER & JUDUL DI TENGAH 🔥
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // 1. Merge Cell buat Judul di Baris 1 (Dari kolom A sampai L)
                $sheet->mergeCells('A1:L1');

                // 2. Styling Judul (Tengah, Bold, Huruf Gede)
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // 3. Styling Header Tabel (Baris ke 3) -> Bold & Background Abu-abu
                $sheet->getStyle('A3:L3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE5E7EB'] // Abu-abu terang Tailwind
                    ]
                ]);

                // 4. BIKIN BORDER KOTAK-KOTAK (Mulai baris 3 sampai data terakhir)
                $sheet->getStyle('A3:L' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], // Warna Hitam
                        ],
                    ],
                ]);

                // 5. Bikin isi data rata tengah semua (Biar rapi kayak gambarmu)
                $sheet->getStyle('A4:L' . $highestRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);
            },
        ];
    }
}