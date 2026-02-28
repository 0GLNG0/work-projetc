<?php

namespace App\Exports;

use App\Models\MeterListrik;
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
class ExportListrik implements WithMultipleSheets
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
        
        if ($this->lokasiAktif) {
            $daftarLokasi = [$this->lokasiAktif];
        } else {
            $daftarLokasi = MeterListrik::whereNotNull('lokasi')->distinct()->pluck('lokasi');
        }

        foreach ($daftarLokasi as $lok) {
            $sheets[] = new ExportListrikSheet($lok);
        }

        if (count($sheets) === 0) {
            $sheets[] = new ExportListrikSheet('Data Kosong');
        }

        return $sheets;
    }
}

// =========================================================================
// 2. CLASS LEMBARAN (PENGISI DATA PER LOKASI)
// =========================================================================
class ExportListrikSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
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
        return MeterListrik::where('lokasi', $this->lokasi)->orderBy('tanggal', 'asc')->get();
    }

    public function headings(): array
    {
        $date = date('Y');
        return [
            ['LAPORAN METER LISTRIK - LOKASI: ' . strtoupper($this->lokasi) . ' - TAHUN: ' . $date],
            [],
            ['TANGGAL','NOMOR ID', 'Kwh', 'Kwh TOTAL', 'Petugas' ,'LWBP', 'HASIL LWBP', 'WBP ', 'HASIL WBP','KVARH ', 'HASIL KVARH']
        ];
    }

    public function map($row): array
    {
        return [
            date('d-M-Y', strtotime($row->tanggal)),
            $row->nomor_id,
            $row->meter_akhir,
            $row->pemakaian, 
            $row->petugas,
            $row->lwbp_akhir, $row->pemakaian_lwbp,
            $row->wbp_akhir, $row->pemakaian_wbp,
            $row->kvarh_akhir, $row->pemakaian_kvarh,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:K1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                $sheet->getStyle('A3:K3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE5E7EB']]
                ]);

                $sheet->getStyle('A3:K' . $highestRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);
            },
        ];
    }
}