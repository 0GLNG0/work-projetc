<?php

namespace App\Http\Controllers;

use App\Models\MeterReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MeterController extends Controller
{
    public function create()
    {
        $statusMeter = MeterReading::$statusMeter;
        return view('meters.create', compact('statusMeter'));
    }

    public function store(Request $request)
    {
        // STEP 1: Validasi input
        $validated = $request->validate([
            'lokasi' => 'required|string|in:' . implode(',', MeterReading::getAllLokasiOptions()),
            'jam' => 'required',
            'tanggal' => 'required|date|before_or_equal:today',
            'meter_air' => 'nullable|numeric|min:0',
            'meter_listrik' => 'nullable|numeric|min:0',
            'foto' => 'nullable|image|max:2048',
            'keterangan' => 'nullable|string|max:500',
            'status_meter' => 'nullable|in:' . implode(',', array_keys(MeterReading::$statusMeter)),
            'petugas' => 'nullable|string|max:100'
        ]);

        // STEP 2: Validasi khusus
        if (!$request->filled('meter_air') && !$request->filled('meter_listrik') && !$request->filled('keterangan')) {
            return back()
                ->withInput()
                ->withErrors(['keterangan' => 'Wajib isi meter air/listrik atau berikan keterangan']);
        }

        try {
            DB::beginTransaction();

            // STEP 3: Ambil data SEBELUMNYA (hanya 1 data terakhir)
            $previousReading = MeterReading::where('lokasi', $request->lokasi)
                ->whereNotNull('meter_air')
                ->whereNotNull('meter_listrik')
                ->latest('tanggal')
                ->first();
            
            // ========== LOGIKA PERHITUNGAN METER AIR ==========
            if ($request->filled('meter_air')) {
                if ($previousReading && $previousReading->meter_air) {
                    // Validasi: meter sekarang harus lebih besar dari meter sebelumnya
                    if ($request->meter_air <= $previousReading->meter_air) {
                        DB::rollBack();
                        return back()
                            ->withInput()
                            ->withErrors(['meter_air' => 'Meter air harus lebih besar dari pembacaan sebelumnya (' . $previousReading->meter_air . ' m³)']);
                    }
                    
                    // Simpan meter sebelumnya dan hitung pemakaian
                    $validated['meter_air_sebelumnya'] = $previousReading->meter_air;
                    $validated['pemakaian_air'] = round($request->meter_air - $previousReading->meter_air, 2);
                } else {
                    // Ini data pertama untuk lokasi ini
                    $validated['meter_air_sebelumnya'] = null;
                    $validated['pemakaian_air'] = null;
                }
            }

            // ========== LOGIKA PERHITUNGAN METER LISTRIK ==========
            if ($request->filled('meter_listrik')) {
                if ($previousReading && $previousReading->meter_listrik) {
                    // Validasi: meter sekarang harus lebih besar dari meter sebelumnya
                    if ($request->meter_listrik <= $previousReading->meter_listrik) {
                        DB::rollBack();
                        return back()
                            ->withInput()
                            ->withErrors(['meter_listrik' => 'Meter listrik harus lebih besar dari pembacaan sebelumnya (' . $previousReading->meter_listrik . ' kWh)']);
                    }
                    
                    // Simpan meter sebelumnya dan hitung pemakaian
                    $validated['meter_listrik_sebelumnya'] = $previousReading->meter_listrik;
                    $validated['pemakaian_listrik'] = round($request->meter_listrik - $previousReading->meter_listrik, 2);
                } else {
                    // Ini data pertama untuk lokasi ini
                    $validated['meter_listrik_sebelumnya'] = null;
                    $validated['pemakaian_listrik'] = null;
                }
            }

            // STEP 4: Upload foto
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('meter-readings/' . date('Y/m'), 'public');
                $validated['foto'] = $path;
            }

            // STEP 5: Set default status meter
            if (!$request->filled('status_meter')) {
                $validated['status_meter'] = $request->filled('meter_air') ? 'normal' : 'error';
            }

            // STEP 6: Simpan data
            $reading = MeterReading::create($validated);
            
            DB::commit();

            // STEP 7: Kirim respon sukses dengan detail pemakaian
            $message = '✅ Data berhasil disimpan!';
            
            $pemakaianMsg = [];
            if (isset($validated['pemakaian_air'])) {
                $pemakaianMsg[] = '💧 Air: ' . $validated['pemakaian_air'] . ' m³';
            }
            if (isset($validated['pemakaian_listrik'])) {
                $pemakaianMsg[] = '⚡ Listrik: ' . $validated['pemakaian_listrik'] . ' kWh';
            }
            
            if (!empty($pemakaianMsg)) {
                $message .= ' Pemakaian hari ini: ' . implode(', ', $pemakaianMsg);
            } elseif (!$request->filled('meter_air') || !$request->filled('meter_listrik')) {
                $message = '📝 Data kendala berhasil dicatat!';
            }

            return redirect()->route('home')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    // API untuk ambil data meter sebelumnya
    public function getPreviousMeter($lokasi)
    {
        $previousReading = MeterReading::where('lokasi', $lokasi)
            ->whereNotNull('meter_air')
            ->whereNotNull('meter_listrik')
            ->latest('tanggal')
            ->first();
        
        if ($previousReading) {
            return response()->json([
                'success' => true,
                'meter_air' => $previousReading->meter_air,
                'meter_listrik' => $previousReading->meter_listrik,
                'pemakaian_air_terakhir' => $previousReading->pemakaian_air,
                'pemakaian_listrik_terakhir' => $previousReading->pemakaian_listrik,
                'tanggal' => $previousReading->tanggal->format('d/m/Y'),
                'jam' => $previousReading->jam
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Belum ada data sebelumnya'
        ]);
    }
}