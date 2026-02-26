<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Monitoring Meter')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
<link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <style>
        /* Animasi untuk dropdown */
        [x-cloak] { display: none !important; }
        .fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
        .fade-enter, .fade-leave-to { opacity: 0; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- ========== NAVBAR DENGAN ALPINE.JS ========== -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg sticky top-0 z-50" 
         x-data="{ mobileMenuOpen: false }">
        
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <!-- LOGO (KIRI) -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                        <div class="bg-white p-1.5 rounded-lg group-hover:scale-110 transition-transform">
                            <i class="fas fa-tachometer-alt text-blue-600 text-xl"></i>
                        </div>
                        <span class="font-bold text-lg hidden sm:block">Monitoring Meter</span>
                        <span class="font-bold text-lg sm:hidden">M-Meter</span>
                    </a>
                </div>
                
                <!-- MENU DESKTOP (LAYAR BESAR) - HIDDEN DI HP -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" 
                       class="px-3 py-2 rounded-lg hover:bg-blue-700 transition flex items-center text-sm font-medium">
                        <i class="fas fa-home mr-1"></i>
                        <span>Home</span>
                    </a>
                    <a href="{{ route('meters.create') }}" 
                       class="px-3 py-2 rounded-lg hover:bg-blue-700 transition flex items-center text-sm font-medium">
                        <i class="fas fa-plus-circle mr-1"></i>
                        <span>Input Meter</span>
                    </a>
                    <a href="{{ route('admin.readings.gabungan') }}" 
                       class="px-3 py-2 rounded-lg hover:bg-blue-700 transition flex items-center text-sm font-medium">
                        <i class="fas fa-user-shield mr-1"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.readings') }}" 
                       class="px-3 py-2 rounded-lg hover:bg-blue-700 transition flex items-center text-sm font-medium">
                        <i class="fas fa-list-ul mr-1"></i>
                        <span>Data Meter</span>
                    </a>
                    
                    @if(session('admin_logged_in'))
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-3 py-2 rounded-lg hover:bg-red-600 transition flex items-center text-sm font-medium ml-2">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                    @endif
                </div>
                
                <!-- HAMBURGER BUTTON (HANYA DI HP) -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="inline-flex items-center justify-center p-2 rounded-lg hover:bg-blue-700 transition focus:outline-none">
                        <i class="fas fa-bars text-2xl" x-show="!mobileMenuOpen"></i>
                        <i class="fas fa-times text-2xl" x-show="mobileMenuOpen" x-cloak></i>
                    </button>
                </div>
            </div>
            
            <!-- ===== MOBILE MENU (DROPDOWN) ===== -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 @click.away="mobileMenuOpen = false"
                 class="md:hidden pb-4 space-y-1" x-cloak>
                
                <!-- Home -->
                <a href="{{ route('home') }}" 
                   @click="mobileMenuOpen = false"
                   class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-home w-8 text-center"></i>
                    <span class="ml-2 font-medium">Home</span>
                </a>
                
                <!-- Input Meter -->
                <a href="{{ route('meters.create') }}" 
                   @click="mobileMenuOpen = false"
                   class="flex items-center px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus-circle w-8 text-center"></i>
                    <span class="ml-2 font-medium">Input Meter</span>
                </a>
                
                <!-- Logout (jika login) -->
                @if(session('admin_logged_in'))
                <form action="{{ route('admin.logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" 
                            @click="mobileMenuOpen = false"
                            class="w-full flex items-center px-4 py-3 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-sign-out-alt w-8 text-center"></i>
                        <span class="ml-2 font-medium">Logout</span>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </nav>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="flex-1 container mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
        
        <!-- ALERT SUCCESS -->
        @if(session('success'))
            <div class="mb-4 lg:mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow flex justify-between items-center"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-green-700 hover:text-green-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- ALERT ERROR -->
        @if(session('error'))
            <div class="mb-4 lg:mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow flex justify-between items-center"
                 x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-2 text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="text-red-700 hover:text-red-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- VALIDATION ERRORS -->
        @if($errors->any())
            <div class="mb-4 lg:mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                    <span class="font-bold">Terjadi kesalahan:</span>
                </div>
                <ul class="list-disc list-inside text-sm ml-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- ========== FOOTER ========== -->
    <footer class="bg-gray-800 text-white py-4 mt-auto">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} Sistem Monitoring Meter Air & Listrik. PDAM Tirta Dhaha Kota Kediri</p>
        </div>
    </footer>

    <!-- SCRIPT TAMBAHAN -->
    @stack('scripts')
</body>
</html>