@extends('admin.readings')

@section('subcontent')
<!-- FILTER CARD -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-filter mr-2"></i>
            Filter Data Air
        </h3>
    </div>
    
    <div class="p-6">
        <form method="GET" action="{{ route('admin.readings.air') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status_meter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Semua Status</option>
                        <option value="normal" {{ request('status_meter') == 'normal' ? 'selected' : '' }}>✅ Normal</option>
                        <option value="error" {{ request('status_meter') == 'error' ? 'selected' : '' }}>❌ Error</option>
                        <option value="perbaikan" {{ request('status_meter') == 'perbaikan' ? 'selected' : '' }}>🔧 Perbaikan</option>
                        <option value="gangguan" {{ request('status_meter') == 'gangguan' ? 'selected' : '' }}>⚠️ Gangguan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
                <a href="{{ route('admin.readings.air') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
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

<!-- TABEL DATA AIR -->
<div class="space-y-8">
    @forelse($groupedReadings as $lokasi => $dataLokasi)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-blue-200">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-water mr-2"></i> Lokasi: {{ $lokasi }}
                </h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                    {{ $dataLokasi->count() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-blue-50 border-b">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                            <th scope="col" class="px-6 py-3">Meter Awal</th>
                            <th scope="col" class="px-6 py-3">Meter Akhir</th>
                            <th scope="col" class="px-6 py-3">Pemakaian (m³)</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Petugas</th>
                            <th scope="col" class="px-6 py-3">Foto</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataLokasi as $index => $item)
                            <tr class="bg-white border-b hover:bg-blue-50 transition">
                                <td class="px-6 py-4 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">{{ $item->meter_awal ?? '-' }}</td>
                                <td class="px-6 py-4">{{ number_format($item->meter_akhir, 2) }}</td>
                                <td class="px-6 py-4 text-blue-600 font-bold">
                                    {{ $item->pemakaian ? number_format($item->pemakaian, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4">{{ $item->status_meter ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $item->petugas }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                @if($item->foto)
                    <a href="{{ asset('storage/' . $item->foto) }}" target="_blank" 
                       class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                        <i class="fas fa-image mr-1"></i> Lihat
                    </a>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
                                <td class="px-6 py-4 flex space-x-2">
                                    <form action="{{ route('admin.readings.air.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                                    </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow p-8 text-center border border-gray-200">
            <i class="fas fa-folder-open text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Belum ada data Air</h3>
        </div>
    @endforelse
</div>
@endsection