@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- HEADER -->
    <div class="mb-6 md:mb-8">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-tachometer-alt text-blue-600 mr-2 text-lg md:text-xl"></i>
            Dashboard Admin
        </h1>
        <p class="text-sm md:text-base text-gray-600 mt-1">Ringkasan data monitoring meter air dan listrik</p>
    </div>

    <!-- STATISTICS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
        <!-- Card Total Data -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-blue-500 hover:shadow-xl transition">
            <div class="p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Data</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-1">{{ $totalReadings }}</p>
                        <p class="text-xs md:text-sm text-gray-500 mt-2">Semua data tersimpan</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3 md:p-4">
                        <i class="fas fa-database text-blue-600 text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-blue-50 px-4 py-2 text-xs md:text-sm text-blue-700">
                                <i class="fas fa-chart-line mr-1"></i>
                {{ number_format(($totalReadings > 0 ? ($todayReadings/$totalReadings)*100 : 0), 1) }}% dari total keseluruhan
            </div>
        </div>

        <!-- Card Data Hari Ini -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-l-4 border-green-500 hover:shadow-xl transition">
            <div class="p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-500 uppercase tracking-wider">Data Hari Ini</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-800 mt-1">{{ $todayReadings }}</p>
                        <p class="text-xs md:text-sm text-gray-500 mt-2">{{ date('d F Y') }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3 md:p-4">
                        <i class="fas fa-calendar-check text-green-600 text-xl md:text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 px-4 py-2 text-xs md:text-sm text-green-700">
                <i class="fas fa-clock mr-1"></i>
                Update terakhir: {{ now()->format('H:i') }} WIB
            </div>
        </div>
    </div>

    <!-- DATA TERBARU CARD -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 md:mb-8">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 md:px-6 py-3 md:py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base md:text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Data Terbaru
                </h3>
                <span class="bg-white text-blue-600 text-xs md:text-sm px-2 md:px-3 py-1 rounded-full font-medium">
                    {{ $latestReadings->count() }} data terbaru
                </span>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-4 md:p-6">
            @if($latestReadings->count() > 0)
                <!-- Table untuk Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Air</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Listrik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($latestReadings as $index => $reading)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-lg flex items-center justify-center
                                            {{ $reading->grup_lokasi == 'Barat Sungai' ? 'bg-blue-100' : 'bg-green-100' }}">
                                            <i class="fas fa-map-marker-alt text-sm
                                                {{ $reading->grup_lokasi == 'Barat Sungai' ? 'text-blue-600' : 'text-green-600' }}">
                                            </i>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $reading->nama_lokasi }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $reading->grup_lokasi }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reading->tanggal->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reading->jam }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    @if($reading->meter_air)
                                        {{ number_format($reading->meter_air, 2) }} m³
                                        @if($reading->pemakaian_air)
                                            <span class="text-xs text-green-600 block">+{{ number_format($reading->pemakaian_air, 2) }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    @if($reading->meter_listrik)
                                        {{ number_format($reading->meter_listrik, 2) }} kWh
                                        @if($reading->pemakaian_listrik)
                                            <span class="text-xs text-orange-600 block">+{{ number_format($reading->pemakaian_listrik, 2) }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($reading->foto)
                                        <a href="{{ asset('storage/' . $reading->foto) }}" target="_blank" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-image"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.readings') }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Card untuk Mobile -->
                <div class="md:hidden space-y-3">
                    @foreach($latestReadings as $index => $reading)
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-lg flex items-center justify-center
                                    {{ $reading->grup_lokasi == 'Barat Sungai' ? 'bg-blue-100' : 'bg-green-100' }}">
                                    <i class="fas fa-map-marker-alt text-sm
                                        {{ $reading->grup_lokasi == 'Barat Sungai' ? 'text-blue-600' : 'text-green-600' }}">
                                    </i>
                                </div>
                                <div class="ml-2">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $reading->nama_lokasi }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $reading->grup_lokasi }}
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">#{{ $index + 1 }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                            <div>
                                <span class="text-gray-500">Tanggal:</span>
                                <span class="font-medium ml-1">{{ $reading->tanggal->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Jam:</span>
                                <span class="font-medium ml-1">{{ $reading->jam }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Meter Air:</span>
                                <span class="font-medium ml-1">{{ $reading->meter_air ? number_format($reading->meter_air, 2) : '-' }} m³</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Meter Listrik:</span>
                                <span class="font-medium ml-1">{{ $reading->meter_listrik ? number_format($reading->meter_listrik, 2) : '-' }} kWh</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                @if($reading->foto)
                                    <a href="{{ asset('storage/' . $reading->foto) }}" target="_blank" 
                                       class="text-blue-600 text-xs hover:underline">
                                        <i class="fas fa-image mr-1"></i> Lihat Foto
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs">Tidak ada foto</span>
                                @endif
                            </div>
                            <a href="{{ route('admin.readings') }}" class="text-blue-600 text-xs hover:underline">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-8 md:py-12">
                    <div class="bg-gray-100 inline-flex p-3 md:p-4 rounded-full mb-3 md:mb-4">
                        <i class="fas fa-inbox text-gray-400 text-2xl md:text-3xl"></i>
                    </div>
                    <p class="text-sm md:text-base text-gray-500 mb-2">Belum ada data tersedia</p>
                    <p class="text-xs md:text-sm text-gray-400">Silakan input data meter terlebih dahulu</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 md:mt-6">
                <a href="{{ route('admin.readings') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 md:px-6 md:py-3 bg-blue-600 text-white text-sm md:text-base font-medium rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-list-ul mr-2"></i>
                    Lihat Semua Data
                </a>
                <a href="{{ route('meters.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 md:px-6 md:py-3 bg-green-600 text-white text-sm md:text-base font-medium rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Input Data Baru
                </a>
            </div>
        </div>
    </div>

    <!-- QUICK STATS CARD (Opsional) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="bg-white rounded-lg shadow p-3 md:p-4">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-lg p-2 md:p-3 mr-3">
                    <i class="fas fa-water text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Pemakaian Air</p>
                    <p class="text-sm md:text-base font-semibold text-gray-800">
                        {{ number_format($latestReadings->sum('pemakaian_air'), 2) }} m³
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 md:p-4">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-lg p-2 md:p-3 mr-3">
                    <i class="fas fa-bolt text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Pemakaian Listrik</p>
                    <p class="text-sm md:text-base font-semibold text-gray-800">
                        {{ number_format($latestReadings->sum('pemakaian_listrik'), 2) }} kWh
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 md:p-4">
            <div class="flex items-center">
                <div class="bg-red-100 rounded-lg p-2 md:p-3 mr-3">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Data Error</p>
                    <p class="text-sm md:text-base font-semibold text-gray-800">
                        {{ $latestReadings->where('status_meter', 'error')->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-3 md:p-4">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-lg p-2 md:p-3 mr-3">
                    <i class="fas fa-users text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Petugas</p>
                    <p class="text-sm md:text-base font-semibold text-gray-800">
                        {{ $latestReadings->pluck('petugas')->unique()->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection