@extends('admin.readings')

@section('title', 'Data Gabungan Meter Air & Listrik')

@section('subcontent')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-chart-pie text-green-600 mr-2"></i>
            Data Gabungan Meter Air & Listrik
        </h1>
        <p class="text-gray-600">Menampilkan data meter air dan listrik dalam satu tampilan</p>
    </div>

    <!-- FILTER CARD -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Filter Data Gabungan
            </h3>
        </div>
        
        <div class="p-6">
            <form method="GET" action="{{ route('admin.readings.gabungan') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Filter Lokasi -->
                    
                    <!-- Filter Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Tanggal Mulai
                        </label>
                        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <!-- Filter Tanggal Selesai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-alt text-blue-500 mr-1"></i> Tanggal Selesai
                        </label>
                        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <!-- filter bulan tahun -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan & Tahun</label>
                    <input type="month" name="bulan" value="{{ request('bulan') }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                    
                    <!-- Filter Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-exclamation-triangle text-orange-500 mr-1"></i> Status
                        </label>
                        <select name="status_meter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="">Semua Status</option>
                            <option value="normal" {{ request('status_meter') == 'normal' ? 'selected' : '' }}>✅ Normal</option>
                            <option value="error" {{ request('status_meter') == 'error' ? 'selected' : '' }}>❌ Error</option>
                            <option value="perbaikan" {{ request('status_meter') == 'perbaikan' ? 'selected' : '' }}>🔧 Perbaikan</option>
                            <option value="gangguan" {{ request('status_meter') == 'gangguan' ? 'selected' : '' }}>⚠️ Gangguan</option>
                        </select>
                    </div>
                    
                    <!-- Filter Petugas -->
                </div>
                
                <!-- BUTTON FILTER -->
                <div class="flex justify-end space-x-3">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                        <i class="fas fa-filter mr-2"></i>Terapkan Filter
                    </button>
                    <a href="{{ route('admin.readings.gabungan') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
        <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
    <h3 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wider">Filter Lokasi</h3>
    
    <div class="flex flex-wrap gap-2">
        <a href="{{ url()->current() }}" 
           class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 
                  {{ !$lokasiAktif ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            🌍 Semua Lokasi
        </a>
        
        @foreach($daftarLokasi as $lokasi)
            <a href="{{ url()->current() }}?lokasi={{ urlencode($lokasi) }}" 
               class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 
                      {{ $lokasiAktif == $lokasi ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                📍 {{ $lokasi }}
            </a>
        @endforeach
    </div>
</div>
    </div>

    <!-- STATISTIK CARD -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Total Data Air</p>
                <p class="text-2xl font-bold">{{ $totalAir }}</p>
            </div>
            <div class="bg-white/20 p-2 rounded-lg">
                <i class="fas fa-water text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-4 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm">Total Data Listrik</p>
                <p class="text-2xl font-bold">{{ $totalListrik }}</p>
            </div>
            <div class="bg-white/20 p-2 rounded-lg">
                <i class="fas fa-bolt text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Total Pemakaian Air</p>
                <p class="text-2xl font-bold">{{ number_format($totalPemakaianAir, 2) }} m³</p>
            </div>
            <div class="bg-white/20 p-2 rounded-lg">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-4 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">Total Pemakaian Listrik</p>
                <p class="text-2xl font-bold">{{ number_format($totalPemakaianListrik, 2) }} kWh</p>
            </div>
            <div class="bg-white/20 p-2 rounded-lg">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
        </div>
    </div>
</div>

    <!-- TABEL GABUNGAN -->
    <div class="space-y-8">
    @forelse($groupedReadings as $lokasi => $dataLokasi)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-black flex items-center">
                    <i class="fas fa-map-marker-alt text-red-400 mr-2"></i>
                    Lokasi: {{ $lokasi }}
                </h3>
                <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1 rounded-full">
                    {{ $dataLokasi->count() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                            <th scope="col" class="px-6 py-3">Meter Air (m³)</th>
                            <th scope="col" class="px-6 py-3">Meter Listrik (kWh)</th>
                            <th scope="col" class="px-6 py-3">Petugas</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataLokasi as $index => $item)
                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="text-blue-600 font-bold">meter: {{ $item->meter_akhir_air ?? '-' }}</div>
                                    <div class="text-green-600 font-semibold text-xs mt-1 border-t pt-1">M<sup>3</sup>: {{ $item->pemakaian_air ? number_format($item->pemakaian_air, 2) : '-' }}</div>
                                    <div class="text-yellow-600 font-semibold text-xs mt-1 border-t pt-1">L/detik:  @if($item->pemakaian_air > 0)
                                        {{ number_format(($item->pemakaian_air * 1000) / 86400, 2) }}
                                        @else
                                            0.00
                                        @endif</div>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="text-blue-600 font-bold">meter: {{ $item->meter_akhir_listrik ?? '-' }}</div>
                                <div class="text-green-600 font-semibold text-xs mt-1 border-t pt-1">KwH: {{ $item->pemakaian_listrik ? number_format($item->pemakaian_listrik, 2) : '-' }}</div>
                            </td>
                                <td class="px-6 py-4">{{ $item->petugas }}</td>
                                <td>
                        <form action="{{ route('admin.readings.destroyGabungan', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('⚠️ YAKIN INGIN MENGHAPUS?\n\nData Air dan Listrik untuk tanggal ini akan dihapus permanen.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-600 hover:text-white px-3 py-1 rounded-md text-sm font-semibold transition-colors duration-200">
                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                            </button>
                        </form>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow p-8 text-center border border-gray-200">
            <i class="fas fa-folder-open text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Belum ada data</h3>
            <p class="text-gray-500 mt-1">Silakan sesuaikan filter pencarian atau input data baru.</p>
        </div>
    @endforelse
</div>

    <!-- BUTTON ACTION -->
    <div class="mt-6 flex flex-wrap justify-center gap-3">
        <a href="{{ route('admin.readings.air') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-water mr-2"></i>Lihat Data Air
        </a>
        <a href="{{ route('admin.readings.listrik') }}" class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
            <i class="fas fa-bolt mr-2"></i>Lihat Data Listrik
        </a>
        <a href="{{ route('meters.create') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-plus-circle mr-2"></i>Input Data Baru
        </a>
    </div>
</div>
@endsection