@extends('layouts.app')

@section('title', 'Export Data Meter')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-file-export text-green-600 mr-2"></i>
            Export Data Meter
        </h1>
        <p class="text-gray-600">Pilih periode dan wilayah untuk mengexport data</p>
    </div>

    <!-- CARD UTAMA -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Filter Export
            </h3>
        </div>
        
        <div class="p-6">
            <div x-data="{ 
                jenis: 'air', 
                periode: 'harian',
                format: 'excel',
                wilayah: 'semua',
                tanggal: '{{ date('Y-m-d') }}',
                tanggalMulai: '{{ date('Y-m-d') }}',
                tanggalSelesai: '{{ date('Y-m-d', strtotime('+6 days')) }}',
                bulan: '{{ date('m') }}',
                tahun: '{{ date('Y') }}',
                tahunTahunan: '{{ date('Y') }}',
                
                // BASE URL
                baseUrl: window.location.origin
            }">
                
                <!-- PILIH JENIS METER -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <i class="fas fa-tachometer-alt text-gray-500 mr-1"></i>
                        Jenis Meter <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition"
                               :class="jenis == 'air' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-300'">
                            <input type="radio" x-model="jenis" value="air" class="sr-only">
                            <div class="text-center">
                                <i class="fas fa-water text-3xl text-blue-500 mb-2"></i>
                                <span class="block font-medium">Meter Air</span>
                            </div>
                        </label>
                        
                        <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition"
                               :class="jenis == 'listrik' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 hover:border-yellow-300'">
                            <input type="radio" x-model="jenis" value="listrik" class="sr-only">
                            <div class="text-center">
                                <i class="fas fa-bolt text-3xl text-yellow-500 mb-2"></i>
                                <span class="block font-medium">Meter Listrik</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- PILIH WILAYAH -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marked-alt text-gray-500 mr-1"></i>
                        Wilayah <span class="text-red-500">*</span>
                    </label>
                    <select x-model="wilayah" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="semua">🌍 Semua Wilayah</option>
                        <option value="barat">🌊 Barat Sungai</option>
                        <option value="timur">🌅 Timur Sungai</option>
                    </select>
                </div>

                <!-- PILIH PERIODE -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-gray-500 mr-1"></i>
                        Periode <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
                        <button type="button" @click="periode = 'harian'" 
                                class="px-4 py-2 rounded-lg transition"
                                :class="periode == 'harian' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            📅 Harian
                        </button>
                        <button type="button" @click="periode = 'mingguan'"
                                class="px-4 py-2 rounded-lg transition"
                                :class="periode == 'mingguan' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            📊 Mingguan
                        </button>
                        <button type="button" @click="periode = 'bulanan'"
                                class="px-4 py-2 rounded-lg transition"
                                :class="periode == 'bulanan' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            📈 Bulanan
                        </button>
                        <button type="button" @click="periode = 'tahunan'"
                                class="px-4 py-2 rounded-lg transition"
                                :class="periode == 'tahunan' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            📆 Tahunan
                        </button>
                    </div>

                    <!-- INPUT PERIODE - HARIAN -->
                    <div x-show="periode == 'harian'" x-transition>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" x-model="tanggal" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>

                    <!-- INPUT PERIODE - MINGGUAN -->
                    <div x-show="periode == 'mingguan'" x-transition class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" x-model="tanggalMulai" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                            <input type="date" x-model="tanggalSelesai" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                    </div>

                    <!-- INPUT PERIODE - BULANAN -->
                    <div x-show="periode == 'bulanan'" x-transition class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select x-model="bulan" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select x-model="tahun" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- INPUT PERIODE - TAHUNAN -->
                    <div x-show="periode == 'tahunan'" x-transition>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <select x-model="tahunTahunan" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                            @for($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- FORMAT EXPORT -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file-alt text-gray-500 mr-1"></i>
                        Format Export
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition"
                               :class="format == 'pdf' ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-red-300'">
                            <input type="radio" x-model="format" value="pdf" class="sr-only">
                            <div class="text-center">
                                <i class="fas fa-file-pdf text-3xl text-red-500 mb-2"></i>
                                <span class="block font-medium">PDF</span>
                            </div>
                        </label>
                        
                        <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition"
                               :class="format == 'excel' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-300'">
                            <input type="radio" x-model="format" value="excel" class="sr-only">
                            <div class="text-center">
                                <i class="fas fa-file-excel text-3xl text-green-500 mb-2"></i>
                                <span class="block font-medium">Excel</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- TOMBOL EXPORT - PAKAI LOGIKA LANGSUNG -->
                 <div class="flex justify-end space-x-3 pt-4">
                    <button @click="
    const url = `${baseUrl}/export/${format}-${jenis}-${periode}`;
    const params = new URLSearchParams({
        wilayah: wilayah,
        ...(periode === 'harian' && { tanggal: tanggal }),
        ...(periode === 'mingguan' && { tanggal_mulai: tanggalMulai, tanggal_selesai: tanggalSelesai }),
        ...(periode === 'bulanan' && { bulan: bulan, tahun: tahun }),
        ...(periode === 'tahunan' && { tahun: tahunTahunan })
    });
    window.location.href = `${url}?${params.toString()}`;
"
class="px-8 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition flex items-center">
    <i class="fas fa-download mr-2"></i>
    Export Data
</button>
                    <a href="{{ route('admin.readings.air') }}" 
                       class="px-8 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection