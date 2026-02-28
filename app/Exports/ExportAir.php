<?php

namespace App\Exports;

use App\Models\MeterAir;
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
// 1. CLASS UTAMA (PEMBUAT BUKU EXCEL)
// =========================================================================
class ExportAir implements WithMultipleSheets
{
    use Exportable;
    protected $lokasiAktif;

    public function __construct($lokasiAktif = null)
    {
        $this->lokasiAktif = $lokasiAktif;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Kalau difilter 1 lokasi, ya bikin 1 sheet aja. Kalau nggak, ambil semua lokasi.
        if ($this->lokasiAktif) {
            $daftarLokasi = [$this->lokasiAktif];
        } else {
            $daftarLokasi = MeterAir::whereNotNull('lokasi')->distinct()->pluck('lokasi');
        }

        foreach ($daftarLokasi as $lok) {
            $sheets[] = new ExportAirSheet($lok);
        }

        // Sabuk Pengaman kalau data kosong
        if (count($sheets) === 0) {
            $sheets[] = new ExportAirSheet('Data Kosong');
        }

        return $sheets;
    }
}

// =========================================================================
// 2. CLASS LEMBARAN (PENGISI DATA PER LOKASI)
// =========================================================================
class ExportAirSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
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
        return MeterAir::where('lokasi', $this->lokasi)->orderBy('tanggal', 'asc')->get();
    }

    public function headings(): array
    {
        $date = date('Y');
        return [
            ['LAPORAN METER AIR - LOKASI: ' . strtoupper($this->lokasi) . ' - TAHUN: ' . $date],
            [], 
            ['Tanggal', 'METER POMPA', 'L/dtk', 'M3','Lokasi', 'petugas']
        ];
    }

    public function map($row): array
    {
       $liter_per_detik = 0;
        if ($row->pemakaian > 0) {
            $liter_per_detik = round(($row->pemakaian * 1000) / 86400, 2);
        }
        return [
            date('d-M-Y', strtotime($row->tanggal)),
            $row->meter_akhir,
            $liter_per_detik,
            $row->pemakaian,
            $row->lokasi,
            $row->petugas,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                $sheet->getStyle('A3:F3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE5E7EB']]
                ]);

                $sheet->getStyle('A3:F' . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
            },
        ];
    }
}