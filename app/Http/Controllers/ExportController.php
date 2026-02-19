<?php

namespace App\Http\Controllers;

use App\Models\MeterReading;
use Illuminate\Http\Request;
use setasign\Fpdf\Fpdf;

class ExportController extends Controller
{
    // ==================== PREVIEW HTML ====================
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
        
        // Apply filters
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
    
    // ==================== PREVIEW PDF DI BROWSER (TANPA DOWNLOAD) ====================
    public function previewPdfHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        $readings = MeterReading::whereDate('tanggal', $tanggal)
            ->orderBy('lokasi')
            ->get();
        
        $pdf = $this->generatePdfHarian($readings, $tanggal);
        
        // I = Inline (tampil di browser), bukan D = Download
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
    
    // ==================== GENERATE PDF (PRIVATE METHODS) ====================
    
    private function generatePdfHarian($readings, $tanggal)
    {
        $pdf = new \FPDF();
        $pdf->AddPage('L', 'A4');
        
        // Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN HARIAN PEMBACAAN METER', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, 'Tanggal: ' . date('d/m/Y', strtotime($tanggal)), 0, 1, 'C');
        $pdf->Ln(5);
        
        // Tabel Header
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(200, 220, 255);
        
        $w = [25, 50, 25, 30, 25, 30, 25, 30, 25];
        
        $pdf->Cell($w[0], 10, 'No', 1, 0, 'C', true);
        $pdf->Cell($w[1], 10, 'Lokasi', 1, 0, 'C', true);
        $pdf->Cell($w[2], 10, 'Jam', 1, 0, 'C', true);
        $pdf->Cell($w[3], 10, 'Meter Air', 1, 0, 'C', true);
        $pdf->Cell($w[4], 10, 'Pakai Air', 1, 0, 'C', true);
        $pdf->Cell($w[5], 10, 'Meter Listrik', 1, 0, 'C', true);
        $pdf->Cell($w[6], 10, 'Pakai Listrik', 1, 0, 'C', true);
        $pdf->Cell($w[7], 10, 'Status', 1, 0, 'C', true);
        $pdf->Cell($w[8], 10, 'Petugas', 1, 1, 'C', true);
        
        // Data
        $pdf->SetFont('Arial', '', 9);
        $no = 1;
        foreach ($readings as $reading) {
            $pdf->Cell($w[0], 8, $no++, 1, 0, 'C');
            $pdf->Cell($w[1], 8, $reading->nama_lokasi, 1);
            $pdf->Cell($w[2], 8, $reading->jam, 1, 0, 'C');
            $pdf->Cell($w[3], 8, $reading->meter_air ? number_format($reading->meter_air, 2) : '-', 1, 0, 'R');
            $pdf->Cell($w[4], 8, $reading->pemakaian_air ? number_format($reading->pemakaian_air, 2) : '-', 1, 0, 'R');
            $pdf->Cell($w[5], 8, $reading->meter_listrik ? number_format($reading->meter_listrik, 2) : '-', 1, 0, 'R');
            $pdf->Cell($w[6], 8, $reading->pemakaian_listrik ? number_format($reading->pemakaian_listrik, 2) : '-', 1, 0, 'R');
            $pdf->Cell($w[7], 8, $reading->status_meter ?? '-', 1, 0, 'C');
            $pdf->Cell($w[8], 8, $reading->petugas ?? '-', 1, 1);
        }
        
        // Footer Total
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, 'Total Data: ' . $readings->count() . ' records', 0, 1);
        
        return $pdf;
    }
    
    private function generatePdfBulanan($readings, $bulan, $tahun)
    {
        $pdf = new \FPDF();
        $pdf->AddPage('L', 'A4');
        
        // Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN BULANAN PEMBACAAN METER', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $bulanTeks = date('F Y', strtotime("$tahun-$bulan-01"));
        $pdf->Cell(0, 8, 'Bulan: ' . $bulanTeks, 0, 1, 'C');
        $pdf->Ln(5);
        
        // Group by lokasi
        $dataPerLokasi = [];
        foreach ($readings as $reading) {
            $dataPerLokasi[$reading->lokasi][] = $reading;
        }
        
        foreach ($dataPerLokasi as $lokasi => $dataLokasi) {
            $namaLokasi = $dataLokasi[0]->nama_lokasi;
            
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->SetFillColor(230, 240, 255);
            $pdf->Cell(0, 10, 'Lokasi: ' . $namaLokasi, 1, 1, 'L', true);
            
            // Tabel
            $w = [15, 30, 35, 25, 35, 25, 30];
            
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(200, 220, 255);
            $pdf->Cell($w[0], 8, 'No', 1, 0, 'C', true);
            $pdf->Cell($w[1], 8, 'Tanggal', 1, 0, 'C', true);
            $pdf->Cell($w[2], 8, 'Meter Air', 1, 0, 'C', true);
            $pdf->Cell($w[3], 8, 'Pakai Air', 1, 0, 'C', true);
            $pdf->Cell($w[4], 8, 'Meter Listrik', 1, 0, 'C', true);
            $pdf->Cell($w[5], 8, 'Pakai Listrik', 1, 0, 'C', true);
            $pdf->Cell($w[6], 8, 'Keterangan', 1, 1, 'C', true);
            
            $pdf->SetFont('Arial', '', 9);
            $no = 1;
            $totalAir = 0;
            $totalListrik = 0;
            
            foreach ($dataLokasi as $reading) {
                $pdf->Cell($w[0], 7, $no++, 1, 0, 'C');
                $pdf->Cell($w[1], 7, $reading->tanggal->format('d/m/Y'), 1, 0, 'C');
                $pdf->Cell($w[2], 7, number_format($reading->meter_air, 2), 1, 0, 'R');
                $pdf->Cell($w[3], 7, $reading->pemakaian_air ? number_format($reading->pemakaian_air, 2) : '-', 1, 0, 'R');
                $pdf->Cell($w[4], 7, number_format($reading->meter_listrik, 2), 1, 0, 'R');
                $pdf->Cell($w[5], 7, $reading->pemakaian_listrik ? number_format($reading->pemakaian_listrik, 2) : '-', 1, 0, 'R');
                $pdf->Cell($w[6], 7, $reading->keterangan ? substr($reading->keterangan, 0, 15) : '-', 1, 1);
                
                if ($reading->pemakaian_air) $totalAir += $reading->pemakaian_air;
                if ($reading->pemakaian_listrik) $totalListrik += $reading->pemakaian_listrik;
            }
            
            // Total per lokasi
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(230, 240, 255);
            $pdf->Cell($w[0] + $w[1] + $w[2], 7, 'TOTAL', 1, 0, 'R', true);
            $pdf->Cell($w[3], 7, number_format($totalAir, 2) . ' m³', 1, 0, 'R', true);
            $pdf->Cell($w[4] + $w[5], 7, number_format($totalListrik, 2) . ' kWh', 1, 0, 'R', true);
            $pdf->Cell($w[6], 7, '', 1, 1, 'R', true);
            
            $pdf->Ln(3);
        }
        
        return $pdf;
    }
    
    private function generatePdfSemua($readings, $request)
    {
        $pdf = new \FPDF();
        $pdf->AddPage('L', 'A4');
        
        // Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN SEMUA DATA', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        
        // Filter info
        $filterInfo = [];
        if ($request->filled('lokasi')) $filterInfo[] = 'Lokasi: ' . $request->lokasi;
        if ($request->filled('tanggal_mulai')) $filterInfo[] = 'Dari: ' . $request->tanggal_mulai;
        if ($request->filled('tanggal_selesai')) $filterInfo[] = 'Sampai: ' . $request->tanggal_selesai;
        
        if (!empty($filterInfo)) {
            $pdf->Cell(0, 8, 'Filter: ' . implode(' | ', $filterInfo), 0, 1);
        }
        $pdf->Ln(5);
        
        // Tabel Header
        $w = [10, 40, 20, 25, 20, 25, 20, 25, 25];
        
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell($w[0], 8, 'No', 1, 0, 'C', true);
        $pdf->Cell($w[1], 8, 'Lokasi', 1, 0, 'C', true);
        $pdf->Cell($w[2], 8, 'Tanggal', 1, 0, 'C', true);
        $pdf->Cell($w[3], 8, 'Meter Air', 1, 0, 'C', true);
        $pdf->Cell($w[4], 8, 'Pakai Air', 1, 0, 'C', true);
        $pdf->Cell($w[5], 8, 'Meter Listrik', 1, 0, 'C', true);
        $pdf->Cell($w[6], 8, 'Pakai Listrik', 1, 0, 'C', true);
        $pdf->Cell($w[7], 8, 'Status', 1, 0, 'C', true);
        $pdf->Cell($w[8], 8, 'Petugas', 1, 1, 'C', true);
        
        // Data
        $pdf->SetFont('Arial', '', 8);
        $no = 1;
        foreach ($readings as $reading) {
            $pdf->Cell($w[0], 7, $no++, 1, 0, 'C');
            $pdf->Cell($w[1], 7, $reading->nama_lokasi, 1);
            $pdf->Cell($w[2], 7, $reading->tanggal->format('d/m/Y'), 1, 0, 'C');
            $pdf->Cell($w[3], 7, $reading->meter_air ? number_format($reading->meter_air, 2) : '-', 1, 0, 'R');
            $pdf->Cell($w[4], 7, $reading->pemakaian_air ? number_format($reading->pemakaian_air, 2) : '-', 1, 0, 'R');
            $pdf->Cell($w[5], 7, $reading->meter_listrik ? number_format($reading->meter_listrik, 2) : '-', 1, 0, 'R');
            $pdf->Cell($w[6], 7, $reading->pemakaian_listrik ? number_format($reading->pemakaian_listrik, 2) : '-', 1, 0, 'R');
            $pdf->Cell($w[7], 7, $reading->status_meter ?? '-', 1, 0, 'C');
            $pdf->Cell($w[8], 7, $reading->petugas ?? '-', 1, 1);
        }
        
        return $pdf;
    }
    
    // ==================== DOWNLOAD PDF (TETAP ADA) ====================
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
        $readings = MeterReading::whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->orderBy('lokasi')->orderBy('tanggal')->get();
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
}