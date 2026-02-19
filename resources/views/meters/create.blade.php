@extends('layouts.app')

@section('title', 'Input Data Meter')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-8 py-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-white p-2 rounded-lg">
                        <i class="fas fa-tachometer-alt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Input Pembacaan Meter</h1>
                        <p class="text-blue-100">Masukkan angka meter hari ini atau catat kendala</p>
                    </div>
                </div>
            </div>

            <!-- DEBUG ERROR -->
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- Form -->
            <div class="p-8">
                <form action="{{ route('meters.store') }}" method="POST" enctype="multipart/form-data" id="formMeter">
                    @csrf
                    <!-- TAMPILKAN ERROR DETAIL -->
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <strong class="font-bold text-lg">❌ Terjadi Kesalahan:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                            {{ session('success') }}
                        </div>
                    @endif
                    <!-- LOKASI -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i>
                            Lokasi <span class="text-red-500">*</span>
                        </label>
                        <select name="lokasi" id="lokasi"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                            <option value="">-- Pilih Lokasi --</option>
                            <optgroup label="🌊 Barat Sungai">
                                <option value="kuwak1" {{ old('lokasi') == 'kuwak1' ? 'selected' : '' }}>Kuwak 1</option>
                                <option value="pesantren" {{ old('lokasi') == 'pesantren' ? 'selected' : '' }}>Pesantren
                                </option>
                                <option value="tosaren" {{ old('lokasi') == 'tosaren' ? 'selected' : '' }}>Tosaren</option>
                                <option value="kleco" {{ old('lokasi') == 'kleco' ? 'selected' : '' }}>Kleco</option>
                                <option value="ngronggo" {{ old('lokasi') == 'ngronggo' ? 'selected' : '' }}>Ngronggo</option>
                            </optgroup>
                            <optgroup label="🌅 Timur Sungai">
                                <option value="tamanan" {{ old('lokasi') == 'tamanan' ? 'selected' : '' }}>Tamanan</option>
                                <option value="wilis utara" {{ old('lokasi') == 'wilis utara' ? 'selected' : '' }}>Wilis Utara
                                </option>
                                <option value="wilis selatan" {{ old('lokasi') == 'wilis selatan' ? 'selected' : '' }}>Wilis
                                    Selatan</option>
                                <option value="unik" {{ old('lokasi') == 'unik' ? 'selected' : '' }}>Unik</option>
                                <option value="pojok" {{ old('lokasi') == 'pojok' ? 'selected' : '' }}>Pojok</option>
                                <option value="sukorame" {{ old('lokasi') == 'sukorame' ? 'selected' : '' }}>Sukorame</option>
                                <option value="gayam" {{ old('lokasi') == 'gayam' ? 'selected' : '' }}>Gayam</option>
                            </optgroup>
                        </select>
                        @error('lokasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TANGGAL & JAM -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="far fa-calendar text-blue-500 mr-1"></i>
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="far fa-clock text-blue-500 mr-1"></i>
                                Jam <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="jam" id="jam" value="{{ old('jam', date('H:i')) }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>
                    </div>

                    <!-- METER AIR -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-water text-blue-500 mr-1"></i>
                            Meter Air (m³)
                            <span class="text-xs text-gray-500">(Kosongkan jika error)</span>
                        </label>
                        <input type="number" step="0.01" name="meter_air" id="meter_air" value="{{ old('meter_air') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: 1234.56">
                        @error('meter_air')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- METER LISTRIK -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-bolt text-yellow-500 mr-1"></i>
                            Meter Listrik (kWh)
                            <span class="text-xs text-gray-500">(Kosongkan jika error)</span>
                        </label>
                        <input type="number" step="0.01" name="meter_listrik" id="meter_listrik"
                            value="{{ old('meter_listrik') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: 5678.90">
                        @error('meter_listrik')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- STATUS METER -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-exclamation-triangle text-orange-500 mr-1"></i>
                            Status Meter
                        </label>
                        <select name="status_meter" id="status_meter"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Status (Opsional) --</option>
                            @foreach($statusMeter as $value => $label)
                                <option value="{{ $value }}" {{ old('status_meter') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- KETERANGAN - FITUR BARU! -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-edit text-green-500 mr-1"></i>
                            Keterangan / Kendala
                            <span class="text-xs text-gray-500">(Wajib jika meter kosong)</span>
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: Meter air rusak, sedang diperbaiki, angka tidak jelas, dll">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Maksimal 500 karakter
                        </p>
                    </div>

                    <!-- PETUGAS -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-500 mr-1"></i>
                            Nama Petugas
                        </label>
                        <input type="text" name="petugas" id="petugas" value="{{ old('petugas') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan nama petugas">
                    </div>

                    <!-- FOTO -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-camera text-purple-500 mr-1"></i>
                            Foto Bukti
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 transition">
                            <input type="file" name="foto" accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Foto meter rusak/error, bukti perbaikan, dll
                        </p>
                    </div>

                    <!-- VALIDASI REAL-TIME -->
                    <div id="validasiAlert" class="mb-6 hidden">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700" id="validasiMessage"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BUTTONS -->
                    <div class="flex gap-4">
                        <button type="submit"
                            class="flex-1 bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Data
                        </button>
                        <a href="{{ route('home') }}"
                            class="flex-1 bg-gray-100 text-gray-700 font-semibold py-3 px-6 rounded-lg hover:bg-gray-200 transition flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- PANDUAN PENGISIAN -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-clipboard-list text-blue-600 mt-1"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">📋 Panduan Pengisian:</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• ✅ <strong>Normal</strong> - Isi meter air & listrik, keterangan opsional</li>
                        <li>• ❌ <strong>Meter Error/Rusak</strong> - Kosongkan meter, isi keterangan (contoh: "Meter air
                            mati")</li>
                        <li>• 🔧 <strong>Perbaikan</strong> - Kosongkan meter, isi status "Dalam Perbaikan" + keterangan
                        </li>
                        <li>• ⚠️ <strong>Gangguan</strong> - Isi meter jika ada, tambahkan keterangan gangguan</li>
                        <li>• 📸 <strong>Foto</strong> - Sangat disarankan untuk dokumentasi kendala</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // VALIDASI REAL-TIME
        // document.addEventListener('DOMContentLoaded', function () {
        //     const meterAir = document.getElementById('meter_air');
        //     const meterListrik = document.getElementById('meter_listrik');
        //     const keterangan = document.getElementById('keterangan');
        //     const statusMeter = document.getElementById('status_meter');
        //     const validasiAlert = document.getElementById('validasiAlert');
        //     const validasiMessage = document.getElementById('validasiMessage');

        //     function cekValidasi() {
        //         const adaMeterAir = meterAir.value !== '' && parseFloat(meterAir.value) > 0;
        //         const adaMeterListrik = meterListrik.value !== '' && parseFloat(meterListrik.value) > 0;
        //         const adaKeterangan = keterangan.value.trim() !== '';
        //         const adaStatus = statusMeter.value !== '';

        //         // Jika tidak ada meter sama sekali
        //         if (!adaMeterAir && !adaMeterListrik) {
        //             if (!adaKeterangan) {
        //                 validasiMessage.textContent = '⚠️ Meter kosong, wajib mengisi keterangan!';
        //                 validasiAlert.classList.remove('hidden');
        //                 return false;
        //             }
        //             if (!adaStatus) {
        //                 validasiMessage.textContent = '⚠️ Meter kosong, pilih status meter!';
        //                 validasiAlert.classList.remove('hidden');
        //                 return false;
        //             }
        //         }

        //         // Jika ada meter tapi tidak lengkap
        //         if (adaMeterAir && !adaMeterListrik && !adaKeterangan) {
        //             validasiMessage.textContent = '⚠️ Meter listrik kosong, berikan keterangan!';
        //             validasiAlert.classList.remove('hidden');
        //             return false;
        //         }

        //         if (!adaMeterAir && adaMeterListrik && !adaKeterangan) {
        //             validasiMessage.textContent = '⚠️ Meter air kosong, berikan keterangan!';
        //             validasiAlert.classList.remove('hidden');
        //             return false;
        //         }

        //         validasiAlert.classList.add('hidden');
        //         return true;
        //     }

        //     // Event listeners
        //     meterAir.addEventListener('input', cekValidasi);
        //     meterListrik.addEventListener('input', cekValidasi);
        //     keterangan.addEventListener('input', cekValidasi);
        //     statusMeter.addEventListener('change', cekValidasi);

        //     // Set default time
        //     const jamInput = document.getElementById('jam');
        //     if (!jamInput.value) {
        //         const now = new Date();
        //         const hours = String(now.getHours()).padStart(2, '0');
        //         const minutes = String(now.getMinutes()).padStart(2, '0');
        //         jamInput.value = `${hours}:${minutes}`;
        //     }
        // });
    </script>
@endsection