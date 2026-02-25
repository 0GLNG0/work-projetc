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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select name="lokasi" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Semua Lokasi</option>
                        @foreach(App\Models\MeterAir::$lokasiOptions as $grup => $lokasis)
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
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Petugas</label>
                    <input type="text" name="petugas" value="{{ request('petugas') }}" placeholder="Nama petugas" class="w-full px-3 py-2 border rounded-lg">
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

<!-- TABEL DATA AIR -->
<div class="space-y-8">
    @forelse($groupedReadings as $lokasi => $dataLokasi)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-yellow-200">
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-bolt mr-2"></i> Lokasi: {{ $lokasi }}
                </h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                    {{ $dataLokasi->count() }} Data
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-yellow-50 border-b">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Tanggal</th>
                            <th scope="col" class="px-6 py-3">Nomor ID</th>
                            <th scope="col" class="px-6 py-3">Meter Awal</th>
                            <th scope="col" class="px-6 py-3">Meter Akhir</th>
                            <th scope="col" class="px-6 py-3">Pemakaian (kWh)</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Petugas</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataLokasi as $index => $item)
                            <tr class="bg-white border-b hover:bg-yellow-50 transition">
                                <td class="px-6 py-4 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-700">{{ $item->nomor_id ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $item->meter_awal ?? '-' }}</td>
                                <td class="px-6 py-4">{{ number_format($item->meter_akhir, 2) }}</td>
                                <td class="px-6 py-4 text-yellow-600 font-bold">
                                    {{ $item->pemakaian ? number_format($item->pemakaian, 2) : '-' }}
                                </td>
                                <td class="px-6 py-4">{{ $item->status_meter ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $item->petugas }}</td>
                                <td class="px-6 py-4 flex space-x-2">
                                    <form action="{{ route('admin.readings.listrik.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data?')">
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
            <h3 class="text-lg font-medium text-gray-900">Belum ada data Listrik</h3>
        </div>
    @endforelse
</div>
@endsection