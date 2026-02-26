<?php

namespace App\Http\Controllers;

use App\Models\MeterReading;
use App\Models\MeterAir;
use App\Models\MeterListrik;
use App\Exports\MeterAirExport;
use App\Exports\MeterListrikExport;
use App\Exports\MeterReadingsExport;
use App\Exports\MonthlyReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use setasign\Fpdf\Fpdf;

class ExportController extends Controller
{
    // ==================== PREVIEW HTML (Menggunakan Model Lama) ====================
    public function previewHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        $readings = MeterReading::whereDate('tanggal', $tanggal)
            ->orderBy('lokasi')
            ->get();
        
        $totalAir = $readings->sum('pemakaian_air');
        $totalListrik = $readings->sum('pemakaian_listrik');
        
        return view('exports.preview-harian', compact('readings', 'tanggal', 'totalAir', 'totalListrik'));
    }
    
    public function previewBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        
        $readings = MeterReading::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('lokasi')
            ->orderBy('tanggal')
            ->get();
        
        // Group by lokasi
        $dataPerLokasi = [];
        foreach ($readings as $reading) {
            $dataPerLokasi[$reading->lokasi][] = $reading;
        }
        
        // Totals
        $totalAir = $readings->sum('pemakaian_air');
        $totalListrik = $readings->sum('pemakaian_listrik');
        
        return view('exports.preview-bulanan', compact('dataPerLokasi', 'bulan', 'tahun', 'totalAir', 'totalListrik'));
    }
    
    public function previewSemua(Request $request)
    {
        $query = MeterReading::query();
        
        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }
        if ($request->filled('status_meter')) {
            $query->where('status_meter', $request->status_meter);
        }
        
        $readings = $query->orderBy('tanggal', 'desc')->get();
        
        return view('exports.preview-semua', compact('readings', 'request'));
    }
    
    // ==================== PREVIEW PDF (Model Lama) ====================
    public function previewPdfHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        $readings = MeterReading::whereDate('tanggal', $tanggal)
            ->orderBy('lokasi')
            ->get();
        
        $pdf = $this->generatePdfHarian($readings, $tanggal);
        
        return response($pdf->Output('I', 'preview_harian_' . $tanggal . '.pdf'))
            ->header('Content-Type', 'application/pdf');
    }
    
    public function previewPdfBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        
        $readings = MeterReading::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('lokasi')
            ->orderBy('tanggal')
            ->get();
        
        $pdf = $this->generatePdfBulanan($readings, $bulan, $tahun);
        
        return response($pdf->Output('I', 'preview_bulanan_' . $tahun . '_' . $bulan . '.pdf'))
            ->header('Content-Type', 'application/pdf');
    }
    
    public function previewPdfSemua(Request $request)
    {
        $query = MeterReading::query();
        
        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }
        
        $readings = $query->orderBy('tanggal', 'desc')->get();
        
        $pdf = $this->generatePdfSemua($readings, $request);
        
        return response($pdf->Output('I', 'preview_semua_data.pdf'))
            ->header('Content-Type', 'application/pdf');
    }
    
    // ==================== DOWNLOAD PDF (Model Lama) ====================
    public function pdfHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $readings = MeterReading::whereDate('tanggal', $tanggal)->orderBy('lokasi')->get();
        $pdf = $this->generatePdfHarian($readings, $tanggal);
        
        return response($pdf->Output('D', 'laporan_harian_' . $tanggal . '.pdf'))
            ->header('Content-Type', 'application/pdf');
    }
    
    public function pdfBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $readings = MeterReading::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->orderBy('lokasi')
            ->orderBy('tanggal')
            ->get();
        $pdf = $this->generatePdfBulanan($readings, $bulan, $tahun);
        
        return response($pdf->Output('D', 'laporan_bulanan_' . $tahun . '_' . $bulan . '.pdf'))
            ->header('Content-Type', 'application/pdf');
    }
    
    public function pdfSemua(Request $request)
    {
        $query = MeterReading::query();
        if ($request->filled('lokasi')) $query->where('lokasi', $request->lokasi);
        if ($request->filled('tanggal_mulai')) $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        if ($request->filled('tanggal_selesai')) $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        
        $readings = $query->orderBy('tanggal', 'desc')->get();
        $pdf = $this->generatePdfSemua($readings, $request);
        
        return response($pdf->Output('D', 'laporan_semua_data.pdf'))
            ->header('Content-Type', 'application/pdf');
    }

    // ==================== EXPORT EXCEL (Model Lama) ====================
    public function excelSemua(Request $request)
    {
        $fileName = 'data_meter_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download(new MeterReadingsExport($request), $fileName);
    }

    public function excelHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $fileName = 'laporan_harian_' . $tanggal . '.xlsx';
        
        $filterRequest = new Request([
            'tanggal_mulai' => $tanggal,
            'tanggal_selesai' => $tanggal,
            'lokasi' => $request->lokasi,
            'status_meter' => $request->status_meter,
        ]);
        
        return Excel::download(new MeterReadingsExport($filterRequest), $fileName);
    }

    public function excelBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        
        $tanggalMulai = "$tahun-$bulan-01";
        $tanggalSelesai = date('Y-m-t', strtotime($tanggalMulai));
        
        $fileName = 'laporan_bulanan_' . $tahun . '_' . $bulan . '.xlsx';
        
        $filterRequest = new Request([
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
        ]);
        
        return Excel::download(new MeterReadingsExport($filterRequest), $fileName);
    }

    public function excelTahunan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $fileName = 'laporan_tahunan_' . $tahun . '.xlsx';
        
        $filterRequest = new Request([
            'tanggal_mulai' => $tahun . '-01-01',
            'tanggal_selesai' => $tahun . '-12-31',
        ]);
        
        return Excel::download(new MeterReadingsExport($filterRequest), $fileName);
    }


    // ==================== EXPORT AIR (Model Baru) ====================
    
    /**
     * GET LOKASI BERDASARKAN WILAYAH
     */
    private function getLokasiByWilayah($wilayah)
    {
        $lokasiOptions = MeterAir::$lokasiOptions;
        
        if ($wilayah == 'barat') {
            return array_keys($lokasiOptions['Barat Sungai']);
        } elseif ($wilayah == 'timur') {
            return array_keys($lokasiOptions['Timur Sungai']);
        } else {
            $all = [];
            foreach ($lokasiOptions as $group) {
                $all = array_merge($all, array_keys($group));
            }
            return $all;
        }
    }

    // ==================== GENERATE FILENAME ====================
    private function generateFilename($jenis, $request)
    {
        $wilayah = $request->wilayah ?? 'semua';
        $periode = '';
        
        if ($request->filled('tanggal')) {
            $periode = '_' . $request->tanggal;
        } elseif ($request->filled('tanggal_mulai')) {
            $periode = '_' . $request->tanggal_mulai . '_to_' . $request->tanggal_selesai;
        } elseif ($request->filled('bulan')) {
            $periode = '_' . $request->tahun . '_' . $request->bulan;
        } elseif ($request->filled('tahun')) {
            $periode = '_' . $request->tahun;
        }
        
        return $jenis . '_' . $wilayah . $periode;
    }

    // ==================== EXPORT EXCEL AIR ====================
   public function excelAir(Request $request)
{
    // 1. MULAI QUERY KE DATABASE
    $query = MeterAir::query();
    
    // 2. FILTER LOKASI / WILAYAH
    $lokasiList = $this->getLokasiByWilayah($request->wilayah ?? 'semua');
    $query->whereIn('lokasi', $lokasiList);
    
    // 3. FILTER PERIODE
    if ($request->filled('tanggal')) {
        $query->whereDate('tanggal', $request->tanggal);
    } elseif ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
        $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
    } elseif ($request->filled('bulan') && $request->filled('tahun')) {
        $query->whereYear('tanggal', $request->tahun)
              ->whereMonth('tanggal', $request->bulan);
    } elseif ($request->filled('tahun')) {
        $query->whereYear('tanggal', $request->tahun);
    }
    
    // 4. EKSEKUSI PENGAMBILAN DATA (Jadinya "Collection")
    $readings = $query->orderBy('tanggal', 'desc')->get();
    
    // 5. BUAT NAMA FILE
    $filename = $this->generateFilename('air', $request);

    // 6. LEMPAR DATA KE EXPORT (Pakai $readings, BUKAN $request)
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LaporanExport(), $filename . '.xlsx');
}


   public function excelAirHarian(Request $request)
{
    $tanggal = $request->tanggal ?? date('Y-m-d');
    // Gunakan merge, bukan langsung assign
    $newRequest = new Request($request->all());
    $newRequest->merge(['tanggal' => $tanggal]);
    return $this->excelAir($newRequest);
}

public function excelAirMingguan(Request $request)
{
    $newRequest = new Request($request->all());
    
    if (!$newRequest->filled('tanggal_mulai')) {
        $newRequest->merge(['tanggal_mulai' => date('Y-m-d', strtotime('-7 days'))]);
    }
    if (!$newRequest->filled('tanggal_selesai')) {
        $newRequest->merge(['tanggal_selesai' => date('Y-m-d')]);
    }
    
    return $this->excelAir($newRequest);
}

public function excelAirBulanan(Request $request)
{
    $bulan = $request->bulan ?? date('m');
    $tahun = $request->tahun ?? date('Y');
    
    $newRequest = new Request($request->all());
    $newRequest->merge(['bulan' => $bulan, 'tahun' => $tahun]);
    
    return $this->excelAir($newRequest);
}

public function excelAirTahunan(Request $request)
{
    $tahun = $request->tahun ?? date('Y');
    
    $newRequest = new Request($request->all());
    $newRequest->merge(['tahun' => $tahun]);
    
    return $this->excelAir($newRequest);
}

    // ==================== EXPORT PDF AIR ====================
    public function pdfAir(Request $request)
    {
        $query = MeterAir::query();
        
        // Filter wilayah
        $lokasiList = $this->getLokasiByWilayah($request->wilayah ?? 'semua');
        $query->whereIn('lokasi', $lokasiList);
        
        // Filter periode
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }
        
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun)
                  ->whereMonth('tanggal', $request->bulan);
        }
        
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        
        $readings = $query->orderBy('tanggal', 'desc')->get();
        $pdf = $this->generatePdfAir($readings, $request);
        $filename = $this->generateFilename('air', $request);
        
        return response($pdf->Output('D', $filename . '.pdf'))
            ->header('Content-Type', 'application/pdf');
    }

    public function pdfAirHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $request->merge(['tanggal' => $tanggal]);
        return $this->pdfAir($request);
    }

    public function pdfAirMingguan(Request $request)
    {
        if (!$request->filled('tanggal_mulai')) {
            $request->merge(['tanggal_mulai' => date('Y-m-d', strtotime('-7 days'))]);
        }
        if (!$request->filled('tanggal_selesai')) {
            $request->merge(['tanggal_selesai' => date('Y-m-d')]);
        }
        return $this->pdfAir($request);
    }

    public function pdfAirBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $request->merge(['bulan' => $bulan, 'tahun' => $tahun]);
        return $this->pdfAir($request);
    }

    public function pdfAirTahunan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $request->merge(['tahun' => $tahun]);
        return $this->pdfAir($request);
    }

    // ==================== EXPORT EXCEL LISTRIK ====================
   public function excelListrik(Request $request)
{
    $query = MeterListrik::query();
    
    // Filter wilayah
    $lokasiList = $this->getLokasiByWilayah($request->wilayah ?? 'semua');
    $query->whereIn('lokasi', $lokasiList);
    
    // Filter periode
    if ($request->filled('tanggal')) {
        $query->whereDate('tanggal', $request->tanggal);
    } elseif ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
        $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
    } elseif ($request->filled('bulan') && $request->filled('tahun')) {
        $query->whereYear('tanggal', $request->tahun)
              ->whereMonth('tanggal', $request->bulan);
    } elseif ($request->filled('tahun')) {
        $query->whereYear('tanggal', $request->tahun);
    }
    
    // AMBIL DATANYA (Collection)
    $readings = $query->orderBy('tanggal', 'desc')->get();
    
    $filename = $this->generateFilename('listrik', $request);
    
    // KIRIM $readings KE EXPORT CLASS
    return Excel::download(new MeterListrikExport($readings), $filename . '.xlsx');
}

    public function excelListrikHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $request->merge(['tanggal' => $tanggal]);
        return $this->excelListrik($request);
    }

    public function excelListrikMingguan(Request $request)
    {
        if (!$request->filled('tanggal_mulai')) {
            $request->merge(['tanggal_mulai' => date('Y-m-d', strtotime('-7 days'))]);
        }
        if (!$request->filled('tanggal_selesai')) {
            $request->merge(['tanggal_selesai' => date('Y-m-d')]);
        }
        return $this->excelListrik($request);
    }

    public function excelListrikBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $request->merge(['bulan' => $bulan, 'tahun' => $tahun]);
        return $this->excelListrik($request);
    }

    public function excelListrikTahunan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $request->merge(['tahun' => $tahun]);
        return $this->excelListrik($request);
    }

    // ==================== EXPORT PDF LISTRIK ====================
    public function pdfListrik(Request $request)
    {
        $query = MeterListrik::query();
        
        // Filter wilayah
        $lokasiList = $this->getLokasiByWilayah($request->wilayah ?? 'semua');
        $query->whereIn('lokasi', $lokasiList);
        
        // Filter periode
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }
        
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun)
                  ->whereMonth('tanggal', $request->bulan);
        }
        
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        
        $readings = $query->orderBy('tanggal', 'desc')->get();
        $pdf = $this->generatePdfListrik($readings, $request);
        $filename = $this->generateFilename('listrik', $request);
        
        return response($pdf->Output('D', $filename . '.pdf'))
            ->header('Content-Type', 'application/pdf');
    }

    public function pdfListrikHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $request->merge(['tanggal' => $tanggal]);
        return $this->pdfListrik($request);
    }

    public function pdfListrikMingguan(Request $request)
    {
        if (!$request->filled('tanggal_mulai')) {
            $request->merge(['tanggal_mulai' => date('Y-m-d', strtotime('-7 days'))]);
        }
        if (!$request->filled('tanggal_selesai')) {
            $request->merge(['tanggal_selesai' => date('Y-m-d')]);
        }
        return $this->pdfListrik($request);
    }

    public function pdfListrikBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $request->merge(['bulan' => $bulan, 'tahun' => $tahun]);
        return $this->pdfListrik($request);
    }

    public function pdfListrikTahunan(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $request->merge(['tahun' => $tahun]);
        return $this->pdfListrik($request);
    }

    // ==================== GENERATE PDF AIR ====================
    private function generatePdfAir($readings, $request)
    {
        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        
        // Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN METER AIR', 0, 1, 'C');
        
        // Info filter
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, 'Wilayah: ' . ucfirst($request->wilayah ?? 'Semua'), 0, 1);
        
        if ($request->filled('tanggal')) {
            $pdf->Cell(0, 8, 'Tanggal: ' . date('d/m/Y', strtotime($request->tanggal)), 0, 1);
        } elseif ($request->filled('tanggal_mulai')) {
            $pdf->Cell(0, 8, 'Periode: ' . date('d/m/Y', strtotime($request->tanggal_mulai)) . ' - ' . date('d/m/Y', strtotime($request->tanggal_selesai)), 0, 1);
        } elseif ($request->filled('bulan')) {
            $pdf->Cell(0, 8, 'Bulan: ' . $request->bulan . '/' . $request->tahun, 0, 1);
        } elseif ($request->filled('tahun')) {
            $pdf->Cell(0, 8, 'Tahun: ' . $request->tahun, 0, 1);
        }
        
        $pdf->Ln(5);
        
        // Tabel
        $w = [10, 40, 25, 25, 25, 30, 30, 30, 20];
        
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($w[0], 8, 'No', 1);
        $pdf->Cell($w[1], 8, 'Lokasi', 1);
        $pdf->Cell($w[2], 8, 'Tanggal', 1);
        $pdf->Cell($w[3], 8, 'Meter Awal', 1);
        $pdf->Cell($w[4], 8, 'Meter Akhir', 1);
        $pdf->Cell($w[5], 8, 'Pemakaian', 1);
        $pdf->Cell($w[6], 8, 'Status', 1);
        $pdf->Cell($w[7], 8, 'Petugas', 1);
        $pdf->Cell($w[8], 8, 'Foto', 1);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 8);
        $no = 1;
        foreach ($readings as $reading) {
            $pdf->Cell($w[0], 6, $no++, 1);
            $pdf->Cell($w[1], 6, $reading->nama_lokasi, 1);
            $pdf->Cell($w[2], 6, $reading->tanggal->format('d/m/Y'), 1);
            $pdf->Cell($w[3], 6, $reading->meter_awal ?? '-', 1);
            $pdf->Cell($w[4], 6, number_format($reading->meter_akhir, 2), 1);
            $pdf->Cell($w[5], 6, $reading->pemakaian ? number_format($reading->pemakaian, 2) : '-', 1);
            $pdf->Cell($w[6], 6, $reading->status_meter ?? '-', 1);
            $pdf->Cell($w[7], 6, $reading->petugas, 1);
            $pdf->Cell($w[8], 6, $reading->foto ? 'Ada' : '-', 1);
            $pdf->Ln();
        }
        
        return $pdf;
    }

    // ==================== GENERATE PDF LISTRIK ====================
    private function generatePdfListrik($readings, $request)
    {
        $pdf = new \FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        
        // Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN METER LISTRIK', 0, 1, 'C');
        
        // Info filter
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, 'Wilayah: ' . ucfirst($request->wilayah ?? 'Semua'), 0, 1);
        
        if ($request->filled('tanggal')) {
            $pdf->Cell(0, 8, 'Tanggal: ' . date('d/m/Y', strtotime($request->tanggal)), 0, 1);
        } elseif ($request->filled('tanggal_mulai')) {
            $pdf->Cell(0, 8, 'Periode: ' . date('d/m/Y', strtotime($request->tanggal_mulai)) . ' - ' . date('d/m/Y', strtotime($request->tanggal_selesai)), 0, 1);
        } elseif ($request->filled('bulan')) {
            $pdf->Cell(0, 8, 'Bulan: ' . $request->bulan . '/' . $request->tahun, 0, 1);
        } elseif ($request->filled('tahun')) {
            $pdf->Cell(0, 8, 'Tahun: ' . $request->tahun, 0, 1);
        }
        
        $pdf->Ln(5);
        
        // Tabel
        $w = [10, 40, 30, 25, 25, 25, 30, 30, 20];
        
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($w[0], 8, 'No', 1);
        $pdf->Cell($w[1], 8, 'Lokasi', 1);
        $pdf->Cell($w[2], 8, 'Nomor ID', 1);
        $pdf->Cell($w[3], 8, 'Tanggal', 1);
        $pdf->Cell($w[4], 8, 'Meter Awal', 1);
        $pdf->Cell($w[5], 8, 'Meter Akhir', 1);
        $pdf->Cell($w[6], 8, 'Pemakaian', 1);
        $pdf->Cell($w[7], 8, 'Status', 1);
        $pdf->Cell($w[8], 8, 'Petugas', 1);
        $pdf->Ln();
        
        $pdf->SetFont('Arial', '', 8);
        $no = 1;
        foreach ($readings as $reading) {
            $pdf->Cell($w[0], 6, $no++, 1);
            $pdf->Cell($w[1], 6, $reading->nama_lokasi, 1);
            $pdf->Cell($w[2], 6, $reading->nomor_id ?? '-', 1);
            $pdf->Cell($w[3], 6, $reading->tanggal->format('d/m/Y'), 1);
            $pdf->Cell($w[4], 6, $reading->meter_awal ?? '-', 1);
            $pdf->Cell($w[5], 6, number_format($reading->meter_akhir, 2), 1);
            $pdf->Cell($w[6], 6, $reading->pemakaian ? number_format($reading->pemakaian, 2) : '-', 1);
            $pdf->Cell($w[7], 6, $reading->status_meter ?? '-', 1);
            $pdf->Cell($w[8], 6, $reading->petugas, 1);
            $pdf->Ln();
        }
        
        return $pdf;
    }
}