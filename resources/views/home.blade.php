@extends('layouts.app')

@section('title', 'Home - Monitoring Meter')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-house-door"></i> Selamat Datang</h4>
                </div>
                <div class="card-body text-center py-5">
                    <h1 class="display-4 text-primary mb-4">
                        <i class="bi bi-speedometer2"></i>
                    </h1>
                    <h2 class="mb-4">Sistem Monitoring Meter Air dan Listrik</h2>
                    <p class="lead mb-4">
                        Sistem untuk menginput dan memantau data meter air dan listrik
                    </p>
                    <div class="row mt-5">
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-cloud-arrow-up text-primary"></i> Input Data
                                    </h5>
                                    <p class="card-text">Input data pembacaan meter air dan listrik</p>
                                    <a href="{{ route('meters.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Input Data Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-bar-chart text-success"></i> Monitoring
                                    </h5>
                                    <p class="card-text">Pantau data yang telah diinput</p>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-success">
                                        <i class="bi bi-eye"></i> Lihat Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection