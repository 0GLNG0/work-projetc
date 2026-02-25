@extends('layouts.app')

@section('title', 'Input Data Meter')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-tachometer-alt text-blue-600 mr-2"></i>
            Input Data Meter
        </h1>
        <p class="text-gray-600">Input data meter air dan listrik dalam satu form</p>
    </div>
    

    <!-- FORM UTAMA - SATU FORM UNTUK SEMUA -->
    <form id="formMeter" method="POST" action="{{ route('meters.store.combined') }}" enctype="multipart/form-data">
        @csrf
        
        <!-- ===== SECTION DATA UMUM ===== -->
<!-- ===== SECTION DATA UMUM ===== -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
        Data Umum
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Lokasi -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i>
                Lokasi <span class="text-red-500">*</span>
            </label>
            <select name="lokasi" id="lokasiSelect" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Pilih Lokasi --</option>
                @foreach($lokasiOptions as $grup => $lokasis)
                    <optgroup label="{{ $grup }}">
                        @foreach($lokasis as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        
        <!-- Tanggal -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-calendar-alt text-blue-500 mr-1"></i>
                Tanggal <span class="text-red-500">*</span>
            </label>
            <input type="date" name="tanggal" id="tanggal" 
                   value="{{ date('Y-m-d') }}" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <!-- Jam -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-clock text-blue-500 mr-1"></i>
                Jam <span class="text-red-500">*</span>
            </label>
            <input type="time" name="jam" id="jam" 
                   value="{{ date('H:i') }}" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <!-- Petugas (DROPDOWN DINAMIS) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <i class="fas fa-user text-gray-500 mr-1"></i>
                Nama Petugas <span class="text-red-500">*</span>
            </label>
            <select name="petugas" id="petugasSelect" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Pilih Lokasi Dulu --</option>
            </select>
        </div>
    </div>
    
    <!-- Info Data Kemarin -->
    <div id="infoKemarin" class="mt-4 p-4 bg-blue-50 rounded-lg hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-sm font-medium text-blue-800">Data Air Kemarin:</span>
                <span class="text-sm text-gray-600 ml-2" id="infoAirKemarin">-</span>
            </div>
            <div>
                <span class="text-sm font-medium text-blue-800">Data Listrik Kemarin:</span>
                <span class="text-sm text-gray-600 ml-2" id="infoListrikKemarin">-</span>
            </div>
        </div>
    </div>
</div>

        <!-- ===== 2 CARD GRID: AIR DAN LISTRIK ===== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- ===== CARD METER AIR ===== -->
<!-- ===== CARD METER AIR ===== -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-blue-200" id="cardAir" style="opacity: 0.5; pointer-events: none;">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-water mr-2"></i>
            Meter Air
        </h3>
    </div>
    
    <div class="p-6">
        <!-- Data Kemarin -->
        <div class="bg-blue-50 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-blue-800">Meter Kemarin:</span>
                <span class="text-sm text-gray-600" id="airTanggalKemarin">-</span>
            </div>
            <div class="text-2xl font-bold text-blue-700 mt-1" id="airMeterKemarin">0</div>
            <div class="text-xs text-gray-500 mt-1" id="airPetugasKemarin">-</div>
        </div>
        
        <!-- Input Meter Sekarang -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Meter Sekarang (m³) <span class="text-red-500">*</span>
            </label>
            <input type="number" step="0.01" name="meter_air" id="airMeterSekarang"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="Contoh: 1234.56">
        </div>
        
        <!-- Pemakaian (Real-time) -->
        <div class="bg-green-50 rounded-lg p-3 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-green-800">Pemakaian Air:</span>
                <span class="text-xl font-bold text-green-600" id="airPemakaian">0 m³</span>
            </div>
        </div>
        
        <!-- Status & Keterangan Air -->
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Meter Air</label>
                <select name="status_meter_air" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="normal">✅ Normal</option>
                    <option value="error">❌ Error/Rusak</option>
                    <option value="perbaikan">🔧 Dalam Perbaikan</option>
                    <option value="gangguan">⚠️ Gangguan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Air</label>
                <textarea name="keterangan_air" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="Catatan kendala air..."></textarea>
            </div>
            
            <!-- ===== UPLOAD FOTO AIR ===== -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-camera text-blue-500 mr-1"></i>
                    Foto Meter Air
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 transition">
                    <input type="file" name="foto_air" id="foto_air" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i> Format: JPG, PNG. Maks: 2MB
                </p>
                <!-- Preview Foto Air -->
                <div id="preview_foto_air" class="mt-2 hidden">
                    <img src="" alt="Preview" class="h-20 rounded-lg border">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== CARD METER LISTRIK ===== -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-yellow-200" id="cardListrik" style="opacity: 0.5; pointer-events: none;">
    <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-bolt mr-2"></i>
            Meter Listrik
        </h3>
    </div>
    
    <div class="p-6">
        <!-- NOMOR ID LISTRIK -->
        <div class="bg-yellow-50 rounded-lg p-4 mb-4">
            <label class="block text-sm font-medium text-yellow-800 mb-1">
                <i class="fas fa-hashtag mr-1"></i>
                Nomor ID Listrik <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nomor_id_listrik" id="nomorIdListrik"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                   placeholder="Contoh: ID-2024-001" required>
        </div>
        
        <!-- Data Kemarin -->
        <div class="bg-yellow-50 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-yellow-800">Meter Kemarin:</span>
                <span class="text-sm text-gray-600" id="listrikTanggalKemarin">-</span>
            </div>
            <div class="text-2xl font-bold text-yellow-700 mt-1" id="listrikMeterKemarin">0</div>
            <div class="text-xs text-gray-500 mt-1" id="listrikPetugasKemarin">-</div>
        </div>
        
        <!-- Input Meter Sekarang -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Meter Sekarang (kWh) <span class="text-red-500">*</span>
            </label>
            <input type="number" step="0.01" name="meter_listrik" id="listrikMeterSekarang"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                   placeholder="Contoh: 5678.90">
        </div>
        
        <!-- Pemakaian (Real-time) -->
        <div class="bg-green-50 rounded-lg p-3 mb-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-green-800">Pemakaian Listrik:</span>
                <span class="text-xl font-bold text-green-600" id="listrikPemakaian">0 kWh</span>
            </div>
        </div>
        
        <!-- Status & Keterangan Listrik -->
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Meter Listrik</label>
                <select name="status_meter_listrik" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                    <option value="normal">✅ Normal</option>
                    <option value="error">❌ Error/Rusak</option>
                    <option value="perbaikan">🔧 Dalam Perbaikan</option>
                    <option value="gangguan">⚠️ Gangguan</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Listrik</label>
                <textarea name="keterangan_listrik" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="Catatan kendala listrik..."></textarea>
            </div>
            
            <!-- ===== UPLOAD FOTO LISTRIK ===== -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-camera text-yellow-500 mr-1"></i>
                    Foto Meter Listrik
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-yellow-400 transition">
                    <input type="file" name="foto_listrik" id="foto_listrik" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i> Format: JPG, PNG. Maks: 2MB
                </p>
                <!-- Preview Foto Listrik -->
                <div id="preview_foto_listrik" class="mt-2 hidden">
                    <img src="" alt="Preview" class="h-20 rounded-lg border">
                </div>
            </div>
        </div>
    </div>
</div>
        </div>

        <!-- TOMBOL SIMPAN (SATU UNTUK SEMUA) -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <button type="submit" id="btnSimpan"
                    class="w-full px-6 py-4 bg-green-600 text-white font-bold text-lg rounded-lg hover:bg-green-700 transition flex items-center justify-center">
                <i class="fas fa-save mr-2"></i>
                Simpan Semua Data
            </button>
            <p class="text-xs text-gray-500 text-center mt-2">
                * Pastikan semua data telah diisi dengan benar
            </p>
        </div>
        <!-- Tampilkan error di form -->
@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    </form>
</div>

<script>

     const petugasPerLokasi = @json($petugasPerLokasi);
    
    const lokasiSelect = document.getElementById('lokasiSelect');
    const petugasSelect = document.getElementById('petugasSelect');
    
    // Fungsi untuk mengupdate dropdown petugas berdasarkan lokasi
    function updatePetugasDropdown() {
        const lokasi = lokasiSelect.value;
        
        // Reset dropdown
        petugasSelect.innerHTML = '<option value="">-- Pilih Petugas --</option>';
        petugasSelect.disabled = !lokasi;
        
        if (lokasi && petugasPerLokasi[lokasi]) {
            // Tambahkan opsi petugas
            petugasPerLokasi[lokasi].forEach(petugas => {
                const option = document.createElement('option');
                option.value = petugas;
                option.textContent = petugas;
                petugasSelect.appendChild(option);
            });
        } else {
            petugasSelect.innerHTML = '<option value="">-- Pilih Lokasi Dulu --</option>';
        }
    }
    
    // Event listener saat lokasi berubah
    lokasiSelect.addEventListener('change', function() {
        updatePetugasDropdown();
        
        // ... kode untuk ambil data kemarin (yang sudah ada)
        if (this.value) {
            fetchPreviousData(this.value);
        }
    });
    
    // Fungsi untuk mengambil data kemarin (sudah ada)
    function fetchPreviousData(lokasi) {
        fetch(`/meters/previous-data?lokasi=${lokasi}`)
            .then(res => res.json())
            .then(data => {
                // Update card Air
                if (data.air) {
                    let meterAir = parseFloat(data.air.meter_akhir) || 0;
                    document.getElementById('airMeterKemarin').textContent = meterAir.toFixed(2);
                    document.getElementById('airTanggalKemarin').textContent = data.air.tanggal || '-';
                    document.getElementById('airPetugasKemarin').textContent = data.air.petugas ? `Petugas: ${data.air.petugas}` : '-';
                } else {
                    document.getElementById('airMeterKemarin').textContent = '0';
                    document.getElementById('airTanggalKemarin').textContent = '-';
                    document.getElementById('airPetugasKemarin').textContent = '-';
                }
                
                // Update card Listrik
                if (data.listrik) {
                    let meterListrik = parseFloat(data.listrik.meter_akhir) || 0;
                    document.getElementById('listrikMeterKemarin').textContent = meterListrik.toFixed(2);
                    document.getElementById('listrikTanggalKemarin').textContent = data.listrik.tanggal || '-';
                    document.getElementById('listrikPetugasKemarin').textContent = data.listrik.petugas ? `Petugas: ${data.listrik.petugas}` : '-';
                } else {
                    document.getElementById('listrikMeterKemarin').textContent = '0';
                    document.getElementById('listrikTanggalKemarin').textContent = '-';
                    document.getElementById('listrikPetugasKemarin').textContent = '-';
                }
            });
    }
    // Preview foto air
document.getElementById('foto_air').addEventListener('change', function(e) {
    const preview = document.getElementById('preview_foto_air');
    const file = e.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});

// Preview foto listrik
document.getElementById('foto_listrik').addEventListener('change', function(e) {
    const preview = document.getElementById('preview_foto_listrik');
    const file = e.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const lokasiSelect = document.getElementById('lokasiSelect');
    const cardAir = document.getElementById('cardAir');
    const cardListrik = document.getElementById('cardListrik');
    const infoKemarin = document.getElementById('infoKemarin');
    
    // Data meter kemarin
    let dataKemarin = {
        air: null,
        listrik: null
    };
    
    // Fungsi untuk mengaktifkan/menonaktifkan card
    function setCardsEnabled(lokasiTerpilih) {
        if (lokasiTerpilih) {
            cardAir.style.opacity = '1';
            cardAir.style.pointerEvents = 'auto';
            cardListrik.style.opacity = '1';
            cardListrik.style.pointerEvents = 'auto';
        } else {
            cardAir.style.opacity = '0.5';
            cardAir.style.pointerEvents = 'none';
            cardListrik.style.opacity = '0.5';
            cardListrik.style.pointerEvents = 'none';
            infoKemarin.classList.add('hidden');
        }
    }
    
    // Ambil data kemarin saat lokasi dipilih
    lokasiSelect.addEventListener('change', function() {
        const lokasi = this.value;
        setCardsEnabled(lokasi);
        
        if (lokasi) {
            fetch(`/meters/previous-data?lokasi=${lokasi}`)
                .then(res => res.json())
                .then(data => {
                    dataKemarin = data;
                    
                    // Update info ringkas
                    infoKemarin.classList.remove('hidden');
                    document.getElementById('infoAirKemarin').textContent = 
                        data.air?.meter_akhir ? data.air.meter_akhir.toFixed(2) + ' m³' : 'Belum ada data';
                    document.getElementById('infoListrikKemarin').textContent = 
                        data.listrik?.meter_akhir ? data.listrik.meter_akhir.toFixed(2) + ' kWh' : 'Belum ada data';
                    
                    // Update card Air
                    if (data.air) {
                        document.getElementById('airMeterKemarin').textContent = data.air.meter_akhir ? data.air.meter_akhir.toFixed(2) : '0';
                        document.getElementById('airTanggalKemarin').textContent = data.air.tanggal || '-';
                        document.getElementById('airPetugasKemarin').textContent = data.air.petugas ? `Petugas: ${data.air.petugas}` : '-';
                    } else {
                        document.getElementById('airMeterKemarin').textContent = '0';
                        document.getElementById('airTanggalKemarin').textContent = '-';
                        document.getElementById('airPetugasKemarin').textContent = '-';
                    }
                    
                    // Update card Listrik
                    if (data.listrik) {
                        document.getElementById('listrikMeterKemarin').textContent = data.listrik.meter_akhir ? data.listrik.meter_akhir.toFixed(2) : '0';
                        document.getElementById('listrikTanggalKemarin').textContent = data.listrik.tanggal || '-';
                        document.getElementById('listrikPetugasKemarin').textContent = data.listrik.petugas ? `Petugas: ${data.listrik.petugas}` : '-';
                    } else {
                        document.getElementById('listrikMeterKemarin').textContent = '0';
                        document.getElementById('listrikTanggalKemarin').textContent = '-';
                        document.getElementById('listrikPetugasKemarin').textContent = '-';
                    }
                    
                    // Reset perhitungan
                    hitungPemakaianAir();
                    hitungPemakaianListrik();
                });
        }
    });
    
    // Hitung pemakaian air real-time
    function hitungPemakaianAir() {
        const meterSekarang = parseFloat(document.getElementById('airMeterSekarang').value) || 0;
        const meterKemarin = parseFloat(document.getElementById('airMeterKemarin').textContent) || 0;
        
        if (meterSekarang > 0 && meterKemarin > 0) {
            const pemakaian = meterSekarang - meterKemarin;
            if (pemakaian >= 0) {
                document.getElementById('airPemakaian').textContent = pemakaian.toFixed(2) + ' m³';
            } else {
                document.getElementById('airPemakaian').textContent = '⚠️ Error: Meter lebih kecil';
            }
        } else {
            document.getElementById('airPemakaian').textContent = '0 m³';
        }
    }
    
    // Hitung pemakaian listrik real-time
    function hitungPemakaianListrik() {
        const meterSekarang = parseFloat(document.getElementById('listrikMeterSekarang').value) || 0;
        const meterKemarin = parseFloat(document.getElementById('listrikMeterKemarin').textContent) || 0;
        
        if (meterSekarang > 0 && meterKemarin > 0) {
            const pemakaian = meterSekarang - meterKemarin;
            if (pemakaian >= 0) {
                document.getElementById('listrikPemakaian').textContent = pemakaian.toFixed(2) + ' kWh';
            } else {
                document.getElementById('listrikPemakaian').textContent = '⚠️ Error: Meter lebih kecil';
            }
        } else {
            document.getElementById('listrikPemakaian').textContent = '0 kWh';
        }
    }
    
    document.getElementById('airMeterSekarang').addEventListener('input', hitungPemakaianAir);
    document.getElementById('listrikMeterSekarang').addEventListener('input', hitungPemakaianListrik);
    
// Validasi sebelum submit
document.getElementById('formMeter').addEventListener('submit', function(e) {
    const lokasi = lokasiSelect.value;
    const tanggal = document.getElementById('tanggal').value;
    const jam = document.getElementById('jam').value;
    const petugas = document.getElementById('petugasSelect').value; // <-- DIPERBAIKI
    const nomorIdListrik = document.getElementById('nomorIdListrik').value;
    const meterAir = document.getElementById('airMeterSekarang').value;
    const meterListrik = document.getElementById('listrikMeterSekarang').value;
    
    let errors = [];
    
    if (!lokasi) errors.push('Lokasi harus dipilih');
    if (!tanggal) errors.push('Tanggal harus diisi');
    if (!jam) errors.push('Jam harus diisi');
    if (!petugas) errors.push('Nama petugas harus diisi');
    if (!nomorIdListrik) errors.push('Nomor ID Listrik harus diisi');
    if (!meterAir) errors.push('Meter air harus diisi');
    if (!meterListrik) errors.push('Meter listrik harus diisi');
    
    if (errors.length > 0) {
        e.preventDefault(); // Hentikan form agar tidak terkirim
        alert('❌ Lengkapi data berikut:\n- ' + errors.join('\n- '));
    }
});
});
</script>
@endsection