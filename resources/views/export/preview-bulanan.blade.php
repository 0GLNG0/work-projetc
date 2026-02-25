@extends('layouts.app')

@section('title', 'Preview Laporan Bulanan')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📊 Preview Laporan Bulanan</h1>
            <p class="text-gray-600">{{ date('F Y', strtotime("$tahun-$bulan-01")) }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('export.pdf.bulanan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>
                Download PDF
            </a>
            <a href="{{ route('export.preview-pdf.bulanan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank"
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

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="text-sm text-gray-500">Total Lokasi Aktif</div>
            <div class="text-2xl font-bold">{{ count($dataPerLokasi) }}</div>
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

    <!-- Detail per Lokasi -->
    @foreach($dataPerLokasi as $lokasi => $readings)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                {{ $readings[0]->nama_lokasi }}
            </h3>
        </div>
        
        @php
            $totalAirLokasi = $readings->sum('pemakaian_air');
            $totalListrikLokasi = $readings->sum('pemakaian_listrik');
        @endphp
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter Air</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemakaian Air</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meter Listrik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemakaian Listrik</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($readings as $reading)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm">{{ $reading->tanggal->format('d/m/Y') }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ number_format($reading->meter_air, 2) }}</td>
                        <td class="px-6 py-3 text-sm text-right">
                            @if($reading->pemakaian_air)
                                <span class="font-semibold text-green-600">{{ number_format($reading->pemakaian_air, 2) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-3 text-sm text-right">{{ number_format($reading->meter_listrik, 2) }}</td>
                        <td class="px-6 py-3 text-sm text-right">
                            @if($reading->pemakaian_listrik)
                                <span class="font-semibold text-orange-600">{{ number_format($reading->pemakaian_listrik, 2) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-3 text-sm">{{ $reading->status_meter ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td class="px-6 py-3 text-sm font-bold" colspan="2">TOTAL BULAN INI</td>
                        <td class="px-6 py-3 text-sm font-bold text-green-600 text-right">{{ number_format($totalAirLokasi, 2) }} m³</td>
                        <td class="px-6 py-3 text-sm"></td>
                        <td class="px-6 py-3 text-sm font-bold text-orange-600 text-right">{{ number_format($totalListrikLokasi, 2) }} kWh</td>
                        <td class="px-6 py-3 text-sm"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection