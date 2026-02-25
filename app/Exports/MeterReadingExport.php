<?php

namespace App\Exports;

use App\Models\MeterReading;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MeterReadingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $request;
    protected $rowNumber = 0;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = MeterReading::query();

        // FILTER LOKASI
        if ($this->request->filled('lokasi')) {
            $query->where('lokasi', $this->request->lokasi);
        }

        // FILTER STATUS
        if ($this->request->filled('status_meter')) {
            $query->where('status_meter', $this->request->status_meter);
        }

        // FILTER TANGGAL
        if ($this->request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $this->request->tanggal_mulai);
        }
        if ($this->request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $this->request->tanggal_selesai);
        }

        // FILTER METER AIR
        if ($this->request->filled('meter_air_min')) {
            $query->where('meter_air', '>=', $this->request->meter_air_min);
        }
        if ($this->request->filled('meter_air_max')) {
            $query->where('meter_air', '<=', $this->request->meter_air_max);
        }

        // FILTER METER LISTRIK
        if ($this->request->filled('meter_listrik_min')) {
            $query->where('meter_listrik', '>=', $this->request->meter_listrik_min);
        }
        if ($this->request->filled('meter_listrik_max')) {
            $query->where('meter_listrik', '<=', $this->request->meter_listrik_max);
        }

        // FILTER PETUGAS
        if ($this->request->filled('petugas')) {
            $query->where('petugas', 'LIKE', '%' . $this->request->petugas . '%');
        }

        // FILTER KETERANGAN
        if ($this->request->filled('cari_keterangan')) {
            $query->where('keterangan', 'LIKE', '%' . $this->request->cari_keterangan . '%');
        }

        return $query->latest()->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NO',
            'LOKASI',
            'GRUP',
            'TANGGAL',
            'JAM',
            'METER AIR (m³)',
            'METER AIR SEBELUMNYA',
            'PEMAKAIAN AIR (m³)',
            'METER LISTRIK (kWh)',
            'METER LISTRIK SEBELUMNYA',
            'PEMAKAIAN LISTRIK (kWh)',
            'STATUS',
            'KETERANGAN',
            'PETUGAS',
            'FOTO',
            'DIBUAT',
        ];
    }

    /**
    * @param mixed $reading
    * @return array
    */
    public function map($reading): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $reading->nama_lokasi,
            $reading->grup_lokasi,
            $reading->tanggal->format('d/m/Y'),
            $reading->jam,
            $reading->meter_air ? number_format($reading->meter_air, 2) : '-',
            $reading->meter_air_sebelumnya ? number_format($reading->meter_air_sebelumnya, 2) : '-',
            $reading->pemakaian_air ? number_format($reading->pemakaian_air, 2) : '-',
            $reading->meter_listrik ? number_format($reading->meter_listrik, 2) : '-',
            $reading->meter_listrik_sebelumnya ? number_format($reading->meter_listrik_sebelumnya, 2) : '-',
            $reading->pemakaian_listrik ? number_format($reading->pemakaian_listrik, 2) : '-',
            $reading->status_meter ? ucfirst($reading->status_meter) : '-',
            $reading->keterangan ?? '-',
            $reading->petugas ?? '-',
            $reading->foto ? 'Ada' : 'Tidak Ada',
            $reading->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style untuk seluruh tabel
        $sheet->getStyle('A1:P' . ($this->rowNumber + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);

        // Alignment untuk kolom tertentu
        $sheet->getStyle('E:P')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Wrap text untuk kolom keterangan
        $sheet->getStyle('M')->getAlignment()->setWrapText(true);

        return [];
    }
}