<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;

// Halaman utama
Route::get('/', [DashboardController::class, 'home'])->name('home');

// Routes untuk authentication
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

// ===== ROUTES UNTUK ADMIN (DENGAN MIDDLEWARE) =====
Route::prefix('admin')->middleware(['admin'])->name('admin.')->group(function () {
    // Routes untuk Air
    Route::get('/readings-air', [AdminController::class, 'readingsAir'])->name('readings.air');
    Route::delete('/readings-air/{id}', [AdminController::class, 'destroyAir'])->name('readings.air.destroy');
    
    // Routes untuk Listrik
    Route::get('/readings-listrik', [AdminController::class, 'readingsListrik'])->name('readings.listrik');
    Route::delete('/readings-listrik/{id}', [AdminController::class, 'destroyListrik'])->name('readings.listrik.destroy');
    
    // Routes untuk Gabungan
    Route::get('/readings-gabungan', [AdminController::class, 'readingsGabungan'])->name('readings.gabungan');
    
    // Redirect /readings ke gabungan
    Route::get('/readings', function() {
        return redirect()->route('admin.readings.gabungan');
    })->name('readings');
});

// ===== ROUTES UNTUK INPUT DATA =====
Route::prefix('meters')->name('meters.')->group(function () {
    Route::get('/create', [MeterController::class, 'create'])->name('create');
    Route::get('/previous-data', [MeterController::class, 'getPreviousData'])->name('previous');
    Route::post('/store-air', [MeterController::class, 'storeAir'])->name('store.air');
    Route::post('/store-listrik', [MeterController::class, 'storeListrik'])->name('store.listrik');
    Route::post('/store-combined', [MeterController::class, 'storeCombined'])->name('store.combined');
});

// ===== ROUTES UNTUK EXPORT (GABUNGKAN SEMUA DALAM SATU GROUP) =====
Route::prefix('export')->name('export.')->group(function () {
    
    // Halaman filter export
    Route::get('/filter', function() {
        return view('export.filter');
    })->name('filter');
    
    // ===== EXPORT AIR =====
    // PDF Air
    Route::get('/pdf-air', [ExportController::class, 'pdfAir'])->name('pdf.air');
    Route::get('/pdf-air-harian', [ExportController::class, 'pdfAirHarian'])->name('pdf.air.harian');
    Route::get('/pdf-air-mingguan', [ExportController::class, 'pdfAirMingguan'])->name('pdf.air.mingguan');
    Route::get('/pdf-air-bulanan', [ExportController::class, 'pdfAirBulanan'])->name('pdf.air.bulanan');
    Route::get('/pdf-air-tahunan', [ExportController::class, 'pdfAirTahunan'])->name('pdf.air.tahunan');
    
    // Excel Air
    Route::get('/excel-air', [ExportController::class, 'excelAir'])->name('excel.air');
    Route::get('/excel-air-harian', [ExportController::class, 'excelAirHarian'])->name('excel.air.harian');
    Route::get('/excel-air-mingguan', [ExportController::class, 'excelAirMingguan'])->name('excel.air.mingguan');
    Route::get('/excel-air-bulanan', [ExportController::class, 'excelAirBulanan'])->name('excel.air.bulanan');
    Route::get('/excel-air-tahunan', [ExportController::class, 'excelAirTahunan'])->name('excel.air.tahunan');
    
    // ===== EXPORT LISTRIK =====
    // PDF Listrik
    Route::get('/pdf-listrik', [ExportController::class, 'pdfListrik'])->name('pdf.listrik');
    Route::get('/pdf-listrik-harian', [ExportController::class, 'pdfListrikHarian'])->name('pdf.listrik.harian');
    Route::get('/pdf-listrik-mingguan', [ExportController::class, 'pdfListrikMingguan'])->name('pdf.listrik.mingguan');
    Route::get('/pdf-listrik-bulanan', [ExportController::class, 'pdfListrikBulanan'])->name('pdf.listrik.bulanan');
    Route::get('/pdf-listrik-tahunan', [ExportController::class, 'pdfListrikTahunan'])->name('pdf.listrik.tahunan');
    
    // Excel Listrik
    Route::get('/excel-listrik', [ExportController::class, 'excelListrik'])->name('excel.listrik');
    Route::get('/excel-listrik-harian', [ExportController::class, 'excelListrikHarian'])->name('excel.listrik.harian');
    Route::get('/excel-listrik-mingguan', [ExportController::class, 'excelListrikMingguan'])->name('excel.listrik.mingguan');
    Route::get('/excel-listrik-bulanan', [ExportController::class, 'excelListrikBulanan'])->name('excel.listrik.bulanan');
    Route::get('/excel-listrik-tahunan', [ExportController::class, 'excelListrikTahunan'])->name('excel.listrik.tahunan');
    
    // ===== EXPORT MODEL LAMA (MeterReading) =====
    // Preview HTML
    Route::get('/preview-harian', [ExportController::class, 'previewHarian'])->name('preview.harian');
    Route::get('/preview-bulanan', [ExportController::class, 'previewBulanan'])->name('preview.bulanan');
    Route::get('/preview-semua', [ExportController::class, 'previewSemua'])->name('preview.semua');
    
    // Preview PDF
    Route::get('/preview-pdf-harian', [ExportController::class, 'previewPdfHarian'])->name('preview-pdf.harian');
    Route::get('/preview-pdf-bulanan', [ExportController::class, 'previewPdfBulanan'])->name('preview-pdf.bulanan');
    Route::get('/preview-pdf-semua', [ExportController::class, 'previewPdfSemua'])->name('preview-pdf.semua');
    
    // Download PDF
    Route::get('/pdf-harian', [ExportController::class, 'pdfHarian'])->name('pdf.harian');
    Route::get('/pdf-bulanan', [ExportController::class, 'pdfBulanan'])->name('pdf.bulanan');
    Route::get('/pdf-semua', [ExportController::class, 'pdfSemua'])->name('pdf.semua');
    
    // Excel Model Lama
    Route::get('/excel-semua', [ExportController::class, 'excelSemua'])->name('excel.semua');
    Route::get('/excel-harian', [ExportController::class, 'excelHarian'])->name('excel.harian');
    Route::get('/excel-bulanan', [ExportController::class, 'excelBulanan'])->name('excel.bulanan');
    Route::get('/excel-tahunan', [ExportController::class, 'excelTahunan'])->name('excel.tahunan');
});