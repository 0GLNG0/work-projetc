<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;


// Halaman utama
Route::get('/', [DashboardController::class, 'home'])->name('home');

// Routes untuk input data
Route::prefix('meters')->group(function () {
    Route::get('/create', [MeterController::class, 'create'])->name('meters.create');
    Route::post('/store', [MeterController::class, 'store'])->name('meters.store');
    // Tambahkan route ini setelah route meters.store
Route::get('/meters/previous/{lokasi}', [MeterController::class, 'getPreviousMeter'])
    ->name('meters.previous');
});

// Routes untuk authentication
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

// Routes untuk admin dengan middleware
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/readings', [AdminController::class, 'readings'])->name('admin.readings');
    Route::delete('/readings/{id}', [AdminController::class, 'destroy'])->name('admin.readings.destroy');
});

Route::prefix('export')->name('export.')->group(function () {
    Route::get('/pdf-harian', [ExportController::class, 'pdfHarian'])->name('pdf.harian');
    Route::get('/pdf-bulanan', [ExportController::class, 'pdfBulanan'])->name('pdf.bulanan');
    Route::get('/pdf-semua', [ExportController::class, 'pdfSemua'])->name('pdf.semua');
});

// ==================== ROUTES EXPORT & PREVIEW PDF LENGKAP ====================
Route::prefix('export')->name('export.')->group(function () {
    
    // ========== PREVIEW HTML (HALAMAN PREVIEW) ==========
    // Laporan Harian - Preview HTML
    Route::get('/preview-harian', [ExportController::class, 'previewHarian'])->name('preview.harian');
    
    // Laporan Bulanan - Preview HTML
    Route::get('/preview-bulanan', [ExportController::class, 'previewBulanan'])->name('preview.bulanan');
    
    // Laporan Semua Data - Preview HTML (dengan filter)
    Route::get('/preview-semua', [ExportController::class, 'previewSemua'])->name('preview.semua');
    
    
    // ========== PREVIEW PDF (TAMPIL DI BROWSER) ==========
    // Laporan Harian - Preview PDF (I = inline/browser)
    Route::get('/preview-pdf-harian', [ExportController::class, 'previewPdfHarian'])->name('preview-pdf.harian');
    
    // Laporan Bulanan - Preview PDF (I = inline/browser)
    Route::get('/preview-pdf-bulanan', [ExportController::class, 'previewPdfBulanan'])->name('preview-pdf.bulanan');
    
    // Laporan Semua Data - Preview PDF (I = inline/browser)
    Route::get('/preview-pdf-semua', [ExportController::class, 'previewPdfSemua'])->name('preview-pdf.semua');
    
    
    // ========== DOWNLOAD PDF (FORCE DOWNLOAD) ==========
    // Laporan Harian - Download PDF (D = download)
    Route::get('/pdf-harian', [ExportController::class, 'pdfHarian'])->name('pdf.harian');
    
    // Laporan Bulanan - Download PDF (D = download)
    Route::get('/pdf-bulanan', [ExportController::class, 'pdfBulanan'])->name('pdf.bulanan');
    
    // Laporan Semua Data - Download PDF (D = download)
    Route::get('/pdf-semua', [ExportController::class, 'pdfSemua'])->name('pdf.semua');
    
});