<?php

namespace App\Http\Controllers;

use App\Models\MeterAir;
use App\Models\MeterListrik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MeterController extends Controller
{
    public function create()
    {
        $lokasiOptions = MeterAir::$lokasiOptions;
        $petugasPerLokasi = MeterAir::$petugasPerLokasi;
        $statusMeter = [
            'normal' => '✅ Normal',
            'error' => '❌ Error / Rusak',
            'perbaikan' => '🔧 Dalam Perbaikan',
            'gangguan' => '⚠️ Gangguan Lainnya'
        ];
        
        return view('meters.create', compact('lokasiOptions', 'statusMeter','petugasPerLokasi'));
    }

public function getPreviousData(Request $request)
    {
        $lokasi = $request->lokasi;
        
        $lastAir = MeterAir::where('lokasi', $lokasi)
            ->whereNotNull('meter_akhir')
            ->latest('tanggal')
            ->first();
        
        $lastListrik = MeterListrik::where('lokasi', $lokasi)
            ->whereNotNull('meter_akhir')
            ->latest('tanggal')
            ->first();
        
        return response()->json([
            'air' => $lastAir ? [
                'meter_akhir' => $lastAir->meter_akhir ? (float) $lastAir->meter_akhir : 0,
                'tanggal' => $lastAir->tanggal->format('d/m/Y'),
                'petugas' => $lastAir->petugas
            ] : null,
            'listrik' => $lastListrik ? [
                'meter_akhir' => $lastListrik->meter_akhir ? (float) $lastListrik->meter_akhir : 0,
                
                // TAMBAHAN: Kirim data akhir kemarin ke form biar UX-nya enak
                'lwbp_akhir' => $lastListrik->lwbp_akhir ? (float) $lastListrik->lwbp_akhir : 0,
                'wbp_akhir' => $lastListrik->wbp_akhir ? (float) $lastListrik->wbp_akhir : 0,
                'kvarh_akhir' => $lastListrik->kvarh_akhir ? (float) $lastListrik->kvarh_akhir : 0,
                
                'tanggal' => $lastListrik->tanggal->format('d/m/Y'),
                'petugas' => $lastListrik->petugas
            ] : null
        ]);
    }

    public function storeAir(Request $request)
    {
        $validated = $request->validate([
            'lokasi' => 'required|string|in:' . implode(',', MeterAir::getAllLokasiOptions()),
            'tanggal' => 'required|date',
            'jam' => 'required',
            'meter_akhir' => 'required|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
            'keterangan' => 'nullable|string|max:500',
            'status_meter' => 'nullable|string',
            'petugas' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            
            $lastReading = MeterAir::getLastReading($validated['lokasi']);
            
            if ($lastReading) {
                $validated['meter_awal'] = $lastReading->meter_akhir;
                $validated['pemakaian'] = round($validated['meter_akhir'] - $lastReading->meter_akhir, 2);
                
                if ($validated['pemakaian'] < 0) {
                    return response()->json(['error' => 'Meter air harus lebih besar dari sebelumnya'], 422);
                }
            }

            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('meter-air/' . date('Y/m'), 'public');
                $validated['foto'] = $path;
            }

            MeterAir::create($validated);
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Data air berhasil disimpan']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeListrik(Request $request)
    {
        // Mirip dengan storeAir, ganti MeterAir jadi MeterListrik
        $validated = $request->validate([
            'lokasi' => 'required|string|in:' . implode(',', MeterListrik::getAllLokasiOptions()),
            'tanggal' => 'required|date',
            'jam' => 'required',
            'meter_akhir' => 'required|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
            'keterangan' => 'nullable|string|max:500',
            'status_meter' => 'nullable|string',
            'petugas' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            
            $lastReading = MeterListrik::getLastReading($validated['lokasi']);
            
            if ($lastReading) {
                $validated['meter_awal'] = $lastReading->meter_akhir;
                $validated['pemakaian'] = round($validated['meter_akhir'] - $lastReading->meter_akhir, 2);
                
                if ($validated['pemakaian'] < 0) {
                    return response()->json(['error' => 'Meter listrik harus lebih besar dari sebelumnya'], 422);
                }
            }

            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('meter-listrik/' . date('Y/m'), 'public');
                $validated['foto'] = $path;
            }

            MeterListrik::create($validated);
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Data listrik berhasil disimpan']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function storeCombined(Request $request)
{
    try {
        // Validasi
        $validated = $request->validate([
            'lokasi' => 'required|string',
                'tanggal' => 'required|date',
                'jam' => 'required',
                'petugas' => 'required|string',
                'meter_air' => 'required|numeric|min:0',
                'meter_listrik' => 'required|numeric|min:0',
                'nomor_id_listrik' => 'required|string',
                
                // TAMBAHAN VALIDASI (nullable biar kalau kosong nggak error)
                'lwbp_akhir' => 'nullable|numeric|min:0',
                'wbp_akhir' => 'nullable|numeric|min:0',
                'kvarh_akhir' => 'nullable|numeric|min:0',
                
                'status_meter_air' => 'nullable|string',
                'status_meter_listrik' => 'nullable|string',
                'keterangan_air' => 'nullable|string',
                'keterangan_listrik' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        // ===== CEK DATA AIR =====
        $lastAir = MeterAir::where('lokasi', $request->lokasi)
            ->latest('tanggal')
            ->first();
            
        $dataAir = [
            'lokasi' => $request->lokasi,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'meter_akhir' => $request->meter_air,
            'status_meter' => $request->status_meter_air ?? 'normal',
            'keterangan' => $request->keterangan_air,
            'petugas' => $request->petugas,
        ];
        
        if ($lastAir) {
            $dataAir['meter_awal'] = $lastAir->meter_akhir;
            $dataAir['pemakaian'] = round($request->meter_air - $lastAir->meter_akhir, 2);
        }
        
        // Upload foto air jika ada
if ($request->hasFile('foto_air')) {
    $path = $request->file('foto_air')->store('meter-air/' . date('Y/m'), 'public');
    $dataAir['foto'] = $path;
}
        
        $savedAir = MeterAir::create($dataAir);
        
        // ===== CEK DATA LISTRIK =====
        $lastListrik = MeterListrik::where('lokasi', $request->lokasi)
                ->latest('tanggal')
                ->first();
                
            $dataListrik = [
                'lokasi' => $request->lokasi,
                'tanggal' => $request->tanggal,
                'jam' => $request->jam,
                'nomor_id' => $request->nomor_id_listrik,
                'meter_akhir' => $request->meter_listrik,
                
                // Masukkan input form ke data listrik
                'lwbp_akhir' => $request->lwbp_akhir,
                'wbp_akhir' => $request->wbp_akhir,
                'kvarh_akhir' => $request->kvarh_akhir,
                
                'status_meter' => $request->status_meter_listrik ?? 'normal',
                'keterangan' => $request->keterangan_listrik,
                'petugas' => $request->petugas,
            ];
        if ($lastListrik) {
                // Listrik Utama
                $dataListrik['meter_awal'] = $lastListrik->meter_akhir;
                $dataListrik['pemakaian'] = round($request->meter_listrik - $lastListrik->meter_akhir, 2);
                
                // Logika LWBP
                $dataListrik['lwbp_awal'] = $lastListrik->lwbp_akhir;
                $dataListrik['pemakaian_lwbp'] = round((float)$request->lwbp_akhir - (float)$lastListrik->lwbp_akhir, 2);
                
                // Logika WBP
                $dataListrik['wbp_awal'] = $lastListrik->wbp_akhir;
                $dataListrik['pemakaian_wbp'] = round((float)$request->wbp_akhir - (float)$lastListrik->wbp_akhir, 2);
                
                // Logika KVARH
                $dataListrik['kvarh_awal'] = $lastListrik->kvarh_akhir;
                $dataListrik['pemakaian_kvarh'] = round((float)$request->kvarh_akhir - (float)$lastListrik->kvarh_akhir, 2);
            }
        
        // Upload foto listrik jika ada
if ($request->hasFile('foto_listrik')) {
    $path = $request->file('foto_listrik')->store('meter-listrik/' . date('Y/m'), 'public');
    $dataListrik['foto'] = $path;
}
        
        $savedListrik = MeterListrik::create($dataListrik);
        
        DB::commit();
        
        return redirect()->route('meters.create')
            ->with('success', '✅ Data air dan listrik berhasil disimpan!');
            
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        // Tampilkan error detail
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Gagal: ' . $e->getMessage() . ' di file ' . $e->getFile() . ' baris ' . $e->getLine()]);
    }
}
}