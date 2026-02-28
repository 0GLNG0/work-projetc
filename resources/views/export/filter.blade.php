@extends('layouts.app')

@section('title', 'Pusat Unduh Laporan')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-800 flex items-center justify-center">
            <i class="fas fa-file-export text-green-600 mr-3"></i> Pusat Unduh Laporan
        </h1>
        <p class="text-gray-600 mt-2">Pilih format laporan Excel yang ingin Anda unduh sesuai kebutuhan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Laporan Meter Air</h3>
                <i class="fas fa-tint text-blue-500 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 mb-6">Unduh data rekap khusus pencatatan meter air lengkap dengan hitungan debit (Ltr/Dtk).</p>
            <a href="{{ route('export.air', ['lokasi' => request('lokasi')]) }}" class="block w-full text-center bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white font-semibold py-2.5 rounded-lg transition-colors">
                ⬇️ Download Excel
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Laporan Meter Listrik</h3>
                <i class="fas fa-bolt text-yellow-500 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 mb-6">Unduh data khusus pencatatan meter listrik dengan rincian LWBP, WBP, dan KVARH.</p>
            <a href="{{ route('export.listrik', ['lokasi' => request('lokasi')]) }}" class="block w-full text-center bg-yellow-50 text-yellow-700 hover:bg-yellow-500 hover:text-white font-semibold py-2.5 rounded-lg transition-colors">
                ⬇️ Download Excel
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Laporan Gabungan Harian</h3>
                <i class="fas fa-file-excel text-green-500 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 mb-6">Laporan komplit Air & Listrik.</p>
            <a href="{{ route('export.excel', ['lokasi' => request('lokasi')]) }}" class="block w-full text-center bg-green-50 text-green-700 hover:bg-green-600 hover:text-white font-semibold py-2.5 rounded-lg transition-colors">
                ⬇️ Download Excel
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Rekapitulasi Bulanan</h3>
                <i class="fas fa-calendar-check text-purple-500 text-2xl"></i>
            </div>
            <p class="text-sm text-gray-600 mb-6">Unduh total akumulasi pemakaian per bulan untuk keperluan evaluasi dan manajemen.</p>
            <a href="{{ route('export.bulanan', ['lokasi' => request('lokasi')]) }}" class="block w-full text-center bg-purple-50 text-purple-700 hover:bg-purple-600 hover:text-white font-semibold py-2.5 rounded-lg transition-colors">
                ⬇️ Download Excel
            </a>
        </div>

        <a href="{{route('admin.readings.gabungan')}}" class="block w-full text-center bg-gray-50 text-gray-700 hover:bg-gray-600 hover:text-white font-semibold py-2.5 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Data Pembacaan
        </a>

    </div>
</div>
@endsection