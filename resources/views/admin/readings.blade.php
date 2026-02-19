@extends('layouts.app')

@section('title', 'Data Pembacaan Meter')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-list-ul text-blue-600 mr-2"></i>
            Data Pembacaan Meter
        </h1>
        <p class="text-gray-600 mt-1">Monitoring pemakaian air dan listrik semua lokasi</p>
    </div>

    <!-- FILTER CARD -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
        <div class="bg-blue-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Filter Data
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.readings') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Filter Lokasi - Dropdown dengan Optgroup -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i> Lokasi
                        </label>
                        <select name="lokasi" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Lokasi</option>
                            @foreach(App\Models\MeterReading::$lokasiOptions as $grup => $lokasis)
                                <optgroup label="{{ $grup }}">
                                    @foreach($lokasis as $value => $label)
                                        <option value="{{ $value }}" {{ request('lokasi') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status Meter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-exclamation-triangle text-orange-500 mr-1"></i> Status
                        </label>
                        <select name="status_meter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            @foreach(App\Models\MeterReading::$statusMeter as $value => $label)
                                <option value="{{ $value }}" {{ request('status_meter') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Tanggal Mulai
                        </label>
                        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter Tanggal Selesai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Tanggal Selesai
                        </label>
                        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter Meter Air Min -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-water text-blue-500 mr-1"></i> Air Min (m³)
                        </label>
                        <input type="number" step="0.01" name="meter_air_min" value="{{ request('meter_air_min') }}"
                               placeholder="Min"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter Meter Air Max -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-water text-blue-500 mr-1"></i> Air Max (m³)
                        </label>
                        <input type="number" step="0.01" name="meter_air_max" value="{{ request('meter_air_max') }}"
                               placeholder="Max"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter Meter Listrik Min -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-bolt text-yellow-500 mr-1"></i> Listrik Min (kWh)
                        </label>
                        <input type="number" step="0.01" name="meter_listrik_min" value="{{ request('meter_listrik_min') }}"
                               placeholder="Min"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter Meter Listrik Max -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-bolt text-yellow-500 mr-1"></i> Listrik Max (kWh)
                        </label>
                        <input type="number" step="0.01" name="meter_listrik_max" value="{{ request('meter_listrik_max') }}"
                               placeholder="Max"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter Petugas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user text-gray-500 mr-1"></i> Petugas
                        </label>
                        <input type="text" name="petugas" value="{{ request('petugas') }}"
                               placeholder="Nama petugas"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter Keterangan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-edit text-green-500 mr-1"></i> Cari Keterangan
                        </label>
                        <input type="text" name="cari_keterangan" value="{{ request('cari_keterangan') }}"
                               placeholder="Kata kunci"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- BUTTON FILTER -->
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center">
                        <i class="fas fa-filter mr-2"></i>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('admin.readings') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center">
                        <i class="fas fa-redo mr-2"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    <!-- TOMBOL EXPORT -->
<!-- TOMBOL EXPORT DENGAN ALPINE.JS YANG BENAR -->
<div class="relative" x-data="{ open: false }">
    <!-- Tombol -->
    <button @click="open = !open" 
            @click.away="open = false" 
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
        <i class="fas fa-file-pdf mr-2"></i>
        Export PDF
        <i class="fas fa-chevron-down ml-2" :class="{ 'rotate-180': open }"></i>
    </button>
    
    <!-- Dropdown Menu -->
    <div x-show="open" 
         x-transition
         x-cloak
         @click.away="open = false"
         class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-xl z-50 border border-gray-200"
         style="display: none;">
        
        <!-- Laporan Harian -->
        <div class="px-4 py-2 bg-gray-100 font-semibold text-gray-700 rounded-t-lg">
            📅 Laporan Harian
        </div>
        <a href="{{ route('export.preview-pdf.harian', ['tanggal' => request('tanggal', date('Y-m-d'))]) }}" target="_blank"
           class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition">
            <i class="fas fa-file-pdf text-red-500 w-6"></i> Preview PDF
        </a>
        <a href="{{ route('export.pdf.harian', ['tanggal' => request('tanggal', date('Y-m-d'))]) }}" 
           class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition">
            <i class="fas fa-download text-green-500 w-6"></i> Download PDF
        </a>
        
        <!-- Divider -->
        <div class="border-t border-gray-200 my-1"></div>
        
        <!-- Laporan Bulanan -->
        <div class="px-4 py-2 bg-gray-100 font-semibold text-gray-700">
            📊 Laporan Bulanan
        </div>
        <a href="{{ route('export.preview-pdf.bulanan', ['bulan' => request('bulan', date('m')), 'tahun' => request('tahun', date('Y'))]) }}" target="_blank"
           class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition">
            <i class="fas fa-file-pdf text-red-500 w-6"></i> Preview PDF
        </a>
        <a href="{{ route('export.pdf.bulanan', ['bulan' => request('bulan', date('m')), 'tahun' => request('tahun', date('Y'))]) }}" 
           class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition">
            <i class="fas fa-download text-green-500 w-6"></i> Download PDF
        </a>
        
        <!-- Divider -->
        <div class="border-t border-gray-200 my-1"></div>
        
        <!-- Semua Data -->
        <div class="px-4 py-2 bg-gray-100 font-semibold text-gray-700">
            📋 Semua Data
        </div>
        <a href="{{ route('export.preview-pdf.semua', request()->query()) }}" target="_blank"
           class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition">
            <i class="fas fa-file-pdf text-red-500 w-6"></i> Preview PDF
        </a>
        <a href="{{ route('export.pdf.semua', request()->query()) }}" 
           class="block px-4 py-2 text-gray-800 hover:bg-gray-100 transition">
            <i class="fas fa-download text-green-500 w-6"></i> Download PDF
        </a>
    </div>
</div>

    <!-- TABEL DATA -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
<table class="min-w-full divide-y divide-gray-200">
    <!-- HEADER TABLE -->
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Air (m³)</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemakaian Air</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter Listrik (kWh)</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemakaian Listrik</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
        </tr>
    </thead>
    
    <!-- BODY TABLE -->
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($readings as $reading)
        <tr class="hover:bg-gray-50 transition">
            <!-- 1. Nomor -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ ($readings->currentPage() - 1) * $readings->perPage() + $loop->iteration }}
            </td>
            
            <!-- 2. Lokasi -->
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
            
            <!-- 3. Tanggal -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $reading->tanggal->format('d/m/Y') }}
            </td>
            
            <!-- 4. Jam -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $reading->jam }}
            </td>
            
            <!-- 5. Meter Air -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                @if($reading->meter_air)
                    {{ number_format($reading->meter_air, 2) }}
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            
            <!-- 6. PEMAKAIAN AIR (YANG BENAR) -->
            <td class="px-6 py-4 whitespace-nowrap">
                @if($reading->pemakaian_air)
                    <div class="text-sm font-semibold text-green-600">
                        +{{ number_format($reading->pemakaian_air, 2) }} m³
                    </div>
                    <div class="text-xs text-gray-500">
                        dari {{ number_format($reading->meter_air_sebelumnya, 2) }}
                    </div>
                @elseif($reading->meter_air && !$reading->meter_air_sebelumnya)
                    <span class="text-xs text-gray-500">Data pertama</span>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            
            <!-- 7. Meter Listrik -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                @if($reading->meter_listrik)
                    {{ number_format($reading->meter_listrik, 2) }}
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            
            <!-- 8. PEMAKAIAN LISTRIK -->
            <td class="px-6 py-4 whitespace-nowrap">
                @if($reading->pemakaian_listrik)
                    <div class="text-sm font-semibold text-orange-600">
                        +{{ number_format($reading->pemakaian_listrik, 2) }} kWh
                    </div>
                    <div class="text-xs text-gray-500">
                        dari {{ number_format($reading->meter_listrik_sebelumnya, 2) }}
                    </div>
                @elseif($reading->meter_listrik && !$reading->meter_listrik_sebelumnya)
                    <span class="text-xs text-gray-500">Data pertama</span>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            
            <!-- 9. STATUS -->
            <td class="px-6 py-4 whitespace-nowrap">
                @if($reading->status_meter)
                    @switch($reading->status_meter)
                        @case('normal')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                ✅ Normal
                            </span>
                            @break
                        @case('error')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                ❌ Error/Rusak
                            </span>
                            @break
                        @case('perbaikan')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                🔧 Perbaikan
                            </span>
                            @break
                        @case('gangguan')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                ⚠️ Gangguan
                            </span>
                            @break
                    @endswitch
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            
            <!-- 10. KETERANGAN -->
            <td class="px-6 py-4 max-w-xs">
                @if($reading->keterangan)
                    <div class="group relative">
                        <span class="text-sm text-gray-900 cursor-help border-b border-dashed border-gray-400">
                            📝 {{ Str::limit($reading->keterangan, 30) }}
                        </span>
                        <div class="hidden group-hover:block absolute z-10 w-64 p-2 mt-1 text-sm bg-gray-800 text-white rounded-lg shadow-lg">
                            {{ $reading->keterangan }}
                        </div>
                    </div>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            
            <!-- 11. PETUGAS -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                @if($reading->petugas)
                    <div class="flex items-center">
                        <i class="fas fa-user text-gray-500 mr-1"></i>
                        {{ $reading->petugas }}
                    </div>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            
            <!-- 12. FOTO -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                @if($reading->foto)
                    <a href="{{ asset('storage/' . $reading->foto) }}" target="_blank" 
                       class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                        <i class="fas fa-image mr-1"></i> Lihat
                    </a>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            
            <!-- 13. DIBUAT -->
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ $reading->created_at->format('d/m/Y H:i') }}
            </td>
            
            <!-- 14. AKSI -->
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <form action="{{ route('admin.readings.destroy', $reading->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="14" class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3 block"></i>
                <p class="text-lg font-medium">Tidak ada data</p>
                <a href="{{ route('meters.create') }}" 
                   class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus-circle mr-2"></i>Input Data Baru
                </a>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
        </div>

        <!-- PAGINATION -->
        @if($readings->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Menampilkan {{ $readings->firstItem() ?? 0 }} 
                    - {{ $readings->lastItem() ?? 0 }} 
                    dari {{ $readings->total() }} data
                </div>
                <div class="flex space-x-2">
                    {{ $readings->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- BUTTON ACTION -->
    <div class="mt-6 flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
        <a href="{{ route('meters.create') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-plus-circle mr-2"></i>
            Input Data Baru
        </a>
        <a href="{{ route('admin.dashboard') }}" 
           class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-tachometer-alt mr-2"></i>
            Dashboard
        </a>
        <button onclick="window.print()" 
                class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-print mr-2"></i>
            Cetak Laporan
        </button>
    </div>
</div>

<!-- SCRIPT UNTUK TOOLTIP -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltip untuk keterangan panjang
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    tooltipElements.forEach(el => {
        el.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-sm bg-gray-800 text-white rounded shadow-lg';
            tooltip.textContent = this.dataset.tooltip;
            this.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = rect.height + 5 + 'px';
            tooltip.style.left = '0';
        });
        
        el.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.bg-gray-800');
            if (tooltip) tooltip.remove();
        });
    });
});
</script>
@endsection