@extends('layouts.app')

@section('title', 'Export Data Meter')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-file-export text-green-600 mr-3"></i>
            Export Data Meter
        </h1>
        <p class="text-gray-600 mt-1">Unduh rekapitulasi data meter air dan listrik ke dalam format Excel.</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-file-excel mr-2"></i>
                Tarik Laporan Excel
            </h3>
        </div>

        <div class="p-6 md:p-8">
            <p class="text-gray-600 mb-6">
                Klik tombol di bawah ini untuk mengunduh seluruh data yang telah tercatat.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 items-center">
                
                <a href="{{ route('export.excel', ['lokasi' => request('lokasi')]) }}" 
                   class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-all duration-200">
                    <i class="fas fa-download mr-2"></i> Export ke Excel
                </a>

                <a href="{{ route('admin.readings.air') }}" 
                   class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-all duration-200">
                    Batal
                </a>
                
            </div>
        </div>
        
    </div>
</div>
@endsection