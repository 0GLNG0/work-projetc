@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-primary">Total Data</h5>
                            <h2 class="display-6">{{ $totalReadings }}</h2>
                        </div>
                        <div class="bg-primary rounded-circle p-3">
                            <i class="bi bi-database text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-success">Data Hari Ini</h5>
                            <h2 class="display-6">{{ $todayReadings }}</h2>
                        </div>
                        <div class="bg-success rounded-circle p-3">
                            <i class="bi bi-calendar-check text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Data Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Meter Air</th>
                                    <th>Meter Listrik</th>
                                    <th>Foto</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestReadings as $reading)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $reading->lokasi }}</td>
                                        <td>{{ $reading->tanggal->format('d/m/Y') }}</td>
                                        <td>{{ $reading->jam }}</td>
                                        <td>{{ number_format($reading->meter_air, 2) }} m³</td>
                                        <td>{{ number_format($reading->meter_listrik, 2) }} kWh</td>
                                        <td>
                                            @if($reading->foto)
                                                <a href="{{ asset('storage/' . $reading->foto) }}" target="_blank">
                                                    <i class="bi bi-image"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.readings') }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('admin.readings') }}" class="btn btn-primary">
                            <i class="bi bi-list-ul"></i> Lihat Semua Data
                        </a>
                        <a href="{{ route('meters.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Input Data Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection