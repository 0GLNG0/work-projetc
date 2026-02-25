@extends('layouts.app')

@section('title', 'Preview Laporan Harian')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📋 Preview Laporan Harian</h1>
                <p class="text-gray-600">Tanggal: {{ date('d/m/Y', strtotime($tanggal)) }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('export.pdf.harian', ['tanggal' => $tanggal]) }}"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Download PDF
                </a>
                <a href="{{ route('export.preview-pdf.harian', ['tanggal' => $tanggal]) }}" target="_blank"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    Preview PDF
                </a>
                <a href="{{ route('admin.readings') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-500">Total Data</div>
                <div class="text-2xl font-bold">{{ $readings->count() }}</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-500">Total Pemakaian Air</div>
                <div class="text-2xl font-bold text-blue-600">{{ number_format($totalAir, 2) }} m³</div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <div class="text-sm text-gray-500">Total Pemakaian Listrik</div>
                <div class="text-2xl font-bold text-yellow-600">{{ number_format($totalListrik, 2) }} kWh</div>
            </div>
        </div>

        <!-- Tabel Preview -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter Air</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemakaian Air</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter Listrik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemakaian Listrik
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($readings as $index => $reading)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium">{{ $reading->nama_lokasi }}</div>
                                    <div class="text-xs text-gray-500">{{ $reading->grup_lokasi }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reading->jam }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    {{ $reading->meter_air ? number_format($reading->meter_air, 2) : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    @if($reading->pemakaian_air)
                                        <span class="font-semibold text-green-600">{{ number_format($reading->pemakaian_air, 2) }}
                                            m³</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    {{ $reading->meter_listrik ? number_format($reading->meter_listrik, 2) : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    @if($reading->pemakaian_listrik)
                                        <span
                                            class="font-semibold text-orange-600">{{ number_format($reading->pemakaian_listrik, 2) }}
                                            kWh</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($reading->status_meter)
                                        <span class="px-2 py-1 text-xs rounded-full 
                                                @if($reading->status_meter == 'normal') bg-green-100 text-green-800
                                                @elseif($reading->status_meter == 'error') bg-red-100 text-red-800
                                                @elseif($reading->status_meter == 'perbaikan') bg-yellow-100 text-yellow-800
                                                @else bg-orange-100 text-orange-800 @endif">
                                            {{ $reading->status_meter }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reading->petugas ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                    Tidak ada data untuk tanggal ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection