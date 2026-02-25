@extends('layouts.app')

@section('title', 'Data Pembacaan Meter')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-list-ul text-blue-600 mr-2"></i>
            Data Pembacaan Meter
        </h1>
        <p class="text-gray-600">Monitoring pemakaian air dan listrik semua lokasi</p>
    </div>

    <!-- NAVIGASI TAB -->
    <div class="mb-6">
        <nav class="flex grid grid-cols-4 gap-4">
            <div>
                <a href="{{ route('admin.readings.air') }}" 
                class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('admin.readings.air*') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-water mr-2"></i>
                Meter Air
            </a>
        </div>
        <div>

            <a href="{{ route('admin.readings.listrik') }}" 
            class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('admin.readings.listrik*') ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fas fa-bolt mr-2"></i>
            Meter Listrik
        </a>
    </div>
    <div>
        
        <a href="{{ route('admin.readings.gabungan') }}" 
        class="py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('admin.readings.gabungan*') ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
        <i class="fas fa-chart-pie mr-2"></i>
        Gabungan
    </a>
</div>
<div>
    <a href="{{ route('export.filter') }}" class="py-4 px-1 border-b-2 font-medium text-sm">
<i class="fas fa-download mr-2"></i> Export Data
</a>

</div>
        </nav>
    </div>

    <!-- CONTENT -->
    @yield('subcontent')
</div>
@endsection