<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeterReading;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalReadings = MeterReading::count();
        $todayReadings = MeterReading::whereDate('tanggal', today())->count();
        
        $latestReadings = MeterReading::latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('totalReadings', 'todayReadings', 'latestReadings'));
    }

    public function readings(Request $request)
    {
        $query = MeterReading::query();

        // Filter berdasarkan lokasi
        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_selesai);
        }

        // Filter berdasarkan range meter air
        if ($request->filled('meter_air_min')) {
            $query->where('meter_air', '>=', $request->meter_air_min);
        }

        if ($request->filled('meter_air_max')) {
            $query->where('meter_air', '<=', $request->meter_air_max);
        }

        // Filter berdasarkan range meter listrik
        if ($request->filled('meter_listrik_min')) {
            $query->where('meter_listrik', '>=', $request->meter_listrik_min);
        }

        if ($request->filled('meter_listrik_max')) {
            $query->where('meter_listrik', '<=', $request->meter_listrik_max);
        }

        $readings = $query->latest()->paginate(20);

        return view('admin.readings', compact('readings'));
        
        // Filter berdasarkan status meter
if ($request->filled('status_meter')) {
    $query->where('status_meter', $request->status_meter);
}

// Filter berdasarkan ada/tidak keterangan
if ($request->filled('ada_keterangan')) {
    if ($request->ada_keterangan === 'ya') {
        $query->whereNotNull('keterangan')->where('keterangan', '!=', '');
    } elseif ($request->ada_keterangan === 'tidak') {
        $query->whereNull('keterangan');
    }
}

// Filter pencarian di keterangan
if ($request->filled('cari_keterangan')) {
    $query->where('keterangan', 'like', '%' . $request->cari_keterangan . '%');
}

// Filter berdasarkan petugas
if ($request->filled('petugas')) {
    $query->where('petugas', 'like', '%' . $request->petugas . '%');
}
    }

    public function destroy($id)
    {
        $reading = MeterReading::findOrFail($id);
        $reading->delete();

        return redirect()->route('admin.readings')
            ->with('success', 'Data berhasil dihapus!');
    }
    
}
