@extends('layouts.app')

@section('title', 'Home - Monitoring Meter')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Hero Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header dengan Gradien -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8 md:px-10 md:py-12 text-center">
            <div class="inline-block bg-white/20 backdrop-blur-sm p-4 rounded-full mb-6">
                <i class="fas fa-tachometer-alt text-white text-4xl md:text-5xl"></i>
            </div>
            <h1 class="text-2xl md:text-4xl font-bold text-white mb-2">
                Sistem Monitoring Meter
            </h1>
            <p class="text-blue-100 text-sm md:text-base max-w-2xl mx-auto">
                Aplikasi untuk menginput dan memantau data meter air dan listrik secara real-time
            </p>
        </div>

        <!-- Body Content -->
        <div class="p-6 md:p-10">
            <!-- Welcome Text -->
            <div class="text-center mb-8 md:mb-12">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-3">
                    Selamat Datang di Dashboard Monitoring
                </h2>
                <p class="text-sm md:text-base text-gray-600 max-w-2xl mx-auto">
                    Kelola dan pantau seluruh data pembacaan meter air dan listrik dengan mudah dan efisien
                </p>
            </div>

            <!-- Statistik Singkat (Opsional, jika ada data) -->
            @if(isset($totalReadings))
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8 md:mb-12">
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-600">{{ $totalReadings ?? 0 }}</div>
                    <div class="text-xs md:text-sm text-gray-600">Total Data</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="text-2xl md:text-3xl font-bold text-green-600">{{ $todayReadings ?? 0 }}</div>
                    <div class="text-xs md:text-sm text-gray-600">Data Hari Ini</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <div class="text-2xl md:text-3xl font-bold text-purple-600">{{ $totalLokasi ?? 12 }}</div>
                    <div class="text-xs md:text-sm text-gray-600">Total Lokasi</div>
                </div>
            </div>
            @endif

            <!-- Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                <!-- Card Input Data -->
                <div class="group relative bg-white border-2 border-blue-100 rounded-xl p-6 hover:shadow-lg transition-all hover:border-blue-300 hover:-translate-y-1">
                    <div class="absolute -top-3 left-6">
                        <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            FEATURE #1
                        </span>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition">
                            <i class="fas fa-cloud-upload-alt text-blue-600 text-2xl"></i>
                        </div>
                        
                        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">
                            Input Data Meter
                        </h3>
                        
                        <p class="text-sm md:text-base text-gray-600 mb-4">
                            Input data pembacaan meter air dan listrik terbaru dari berbagai lokasi
                        </p>
                        
                        <ul class="text-left text-xs md:text-sm text-gray-500 mb-6 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                Meter Air & Listrik
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                Upload Foto Dokumentasi
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                Catat Kendala Lapangan
                            </li>
                        </ul>
                        
                        <a href="{{ route('meters.create') }}" 
                           class="inline-flex items-center justify-center w-full px-6 py-3 bg-blue-600 text-white text-sm md:text-base font-medium rounded-lg hover:bg-blue-700 transition group-hover:scale-105">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Input Data Baru
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition"></i>
                        </a>
                    </div>
                </div>

                <!-- Card Monitoring -->
                <div class="group relative bg-white border-2 border-green-100 rounded-xl p-6 hover:shadow-lg transition-all hover:border-green-300 hover:-translate-y-1">
                    <div class="absolute -top-3 left-6">
                        <span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                            FEATURE #2
                        </span>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-green-200 transition">
                            <i class="fas fa-chart-bar text-green-600 text-2xl"></i>
                        </div>
                        
                        <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-2">
                            Monitoring & Analisis
                        </h3>
                        
                        <p class="text-sm md:text-base text-gray-600 mb-4">
                            Pantau dan analisis data pemakaian meter air dan listrik
                        </p>
                        
                        <ul class="text-left text-xs md:text-sm text-gray-500 mb-6 space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                Dashboard Real-time
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                Filter & Pencarian Data
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                Export Laporan PDF
                            </li>
                        </ul>
                        
                        <a href="{{ route('admin.readings.gabungan') }}" 
                           class="inline-flex items-center justify-center w-full px-6 py-3 bg-green-600 text-white text-sm md:text-base font-medium rounded-lg hover:bg-green-700 transition group-hover:scale-105">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Dashboard
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Info Tambahan (Fitur Lainnya) -->

            <!-- Quick Actions untuk Mobile -->
            <div class="mt-6 md:hidden grid grid-cols-2 gap-2">
                <a href="{{ route('meters.create') }}" 
                   class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg">
                    <i class="fas fa-plus-circle mr-1"></i>
                    Input
                </a>
                <a href="{{ route('admin.readings.gabungan') }}" 
                   class="flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg">
                    <i class="fas fa-eye mr-1"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Footer Info Card (Opsional) -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 md:p-6">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="flex items-center mb-3 md:mb-0">
                <div class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">
                    <i class="fas fa-info text-sm"></i>
                </div>
                <span class="text-sm md:text-base text-blue-800">
                    Sistem monitoring terintegrasi untuk seluruh lokasi
                </span>
            </div>
            <div class="flex space-x-2">
                <span class="bg-blue-200 text-blue-800 px-3 py-1 rounded-full text-xs">
                    v2.0.0
                </span>
                <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-xs">
                    Updated {{ date('d M Y') }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection