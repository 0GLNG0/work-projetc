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
        <form method="GET" action="{{ route('admin.readings.listrik') }}" class="space-y-4">
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

                                @if(request('lokasi'))
                    <input type="hidden" name="lokasi" value="{{ request('lokasi') }}">
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulan & Tahun</label>
                    <input type="month" name="bulan" value="{{ request('bulan') }}" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
                <a href="{{ route('admin.readings.listrik') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

    
    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
    <h3 class="text-sm font-bold text-gray-500 mb-3 uppercase tracking-wider">Filter Lokasi</h3>
    
    <div class="flex flex-wrap gap-2">
        <a href="{{ request()->fullUrlWithQuery(['lokasi' => null]) }}" 
           class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 
                  {{ !$lokasiAktif ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            🌍 Semua Lokasi
        </a>
        
        @foreach($daftarLokasi as $lokasi)
            <a href="{{ request()->fullUrlWithQuery(['lokasi' => $lokasi]) }}" 
               class="px-5 py-2 rounded-full text-sm font-semibold transition-all duration-200 
                      {{ $lokasiAktif == $lokasi ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                📍 {{ $lokasi }}
            </a>
        @endforeach
    </div>
</div>
<!-- bukan untuk deploy -->
<!-- <form action="{{ route('import.listrik') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2 bg-white p-3 rounded-lg shadow-sm border border-gray-200">
    @csrf
    <div>
        <label for="file_excel" class="block text-xs font-medium text-gray-700 mb-1">Upload Data (Excel)</label>
        <input type="file" name="file_excel" id="file_excel" required
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
        <button type="submit" class="mt-5 bg-blue-600 text-white px-4 py-1.5 rounded-md hover:bg-blue-700 text-sm font-semibold transition-colors">
            <i class="fas fa-upload mr-1"></i> Import
        </button>
    </form> -->
    <!-- bukan untuk deploy -->
<!-- ========================================================== -->
    <!--=============== TABEL DATA LISTRIK ===============-->
<!-- ========================================================== -->
<div class="space-y-8">
    @forelse($groupedReadings as $lokasi => $dataLokasi)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-blue-200">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-bolt mr-2"></i> Lokasi: {{ $lokasi }}
                </h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                    {{ $dataLokasi->count() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-yellow-50 border-b">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nomor ID</th>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                            <th scope="col" class="px-6 py-3">Meter </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LWBP</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WBP</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KVARH</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Petugas</th>
                            <th scope="col" class="px-6 py-3">Foto</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataLokasi as $index => $item)
                            <tr class="bg-white border-b hover:bg-blue-50 transition">
                                <td class="px-6 py-4">{{ $item->nomor_id }}</td>
                                <td class="px-6 py-4">{{ $item->tanggal->format('d/m/Y') }}</td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="text-blue-600 font-bold">meter: {{ $item->meter_akhir ?? '-' }}</div>
                                <div class="text-green-600 font-semibold text-xs mt-1 border-t pt-1">KwH: {{ $item->pemakaian ? number_format($item->pemakaian, 2) : '-' }}</div>
                            </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div class="font-bold text-blue-600">LWBP: {{ $item->lwbp_akhir ?? 0 }}</div>
                                    <div class="text-green-600 font-semibold text-xs mt-1 border-t pt-1">
                                        Hasil: {{ $item->pemakaian_lwbp ?? 0 }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div class="font-bold text-blue-600">WBP: {{ $item->wbp_akhir ?? 0 }}</div>
                                    <div class="text-green-600 font-semibold text-xs mt-1 border-t pt-1">
                                        Hasil: {{ $item->pemakaian_wbp ?? 0 }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <div class="font-bold text-purple-600">KVARH: {{ $item->kvarh_akhir ?? 0 }}</div>
                                    <div class="text-green-600 font-semibold text-xs mt-1 border-t pt-1">
                                        Hasil: {{ $item->pemakaian_kvarh ?? 0 }}
                                    </div>
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