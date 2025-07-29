<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Penambahan: Custom Easing untuk efek 'spring' atau memantul pada sidebar */
        .transition-spring {
            transition-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="relative min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="sidebarOpen = window.innerWidth >= 1024">
        <div @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden" x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak></div>
        
        {{-- Penambahan: class .transition-spring dan durasi lebih panjang untuk efek memantul --}}
        <aside class="fixed inset-y-0 left-0 bg-[#0b3d91] text-gray-300 w-64 p-6 flex-col z-30 transform transition-transform duration-500 transition-spring" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            {{-- Penambahan: animasi skala saat hover di area admin --}}
            <div class="flex flex-col items-center text-center mb-10">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-20 w-20 mb-3 object-contain transition-transform duration-300 hover:rotate-12">
                <span class="text-xl font-bold tracking-wider">ADMIN</span>
            </div>

            <nav class="flex-1 space-y-2">
                {{-- Penambahan: class 'group', 'transition-all', dan 'hover:translate-x-2' untuk animasi geser. Ikon juga diberi animasi. --}}
                <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-4 py-3 rounded-lg text-white hover:bg-[#1e4e9c] hover:translate-x-2 transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#1e4e9c]' : '' }}">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.mahasiswa.index') }}" class="group flex items-center px-4 py-3 rounded-lg text-white hover:bg-[#1e4e9c] hover:translate-x-2 transition-all duration-200 {{ request()->routeIs('admin.mahasiswa.*') ? 'bg-[#1e4e9c]' : '' }}">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Data Mahasiswa</span>
                </a>

                <a href="{{ route('admin.tagihan.index') }}" class="group flex items-center px-4 py-3 rounded-lg text-white hover:bg-[#1e4e9c] hover:translate-x-2 transition-all duration-200 {{ request()->routeIs('admin.tagihan.*') ? 'bg-[#1e4e9c]' : '' }}">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Data Tagihan</span>
                </a>

                <a href="{{ route('admin.laporan.index') }}" class="group flex items-center px-4 py-3 rounded-lg text-white hover:bg-[#1e4e9c] hover:translate-x-2 transition-all duration-200 {{ request()->routeIs('admin.laporan.*') ? 'bg-[#1e4e9c]' : '' }}">
                    <svg class="w-5 h-5 mr-3 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>Laporan Keuangan</span>
                </a>
            </nav>
        </aside>
        
               <div class="flex-1 flex flex-col transition-all duration-500 transition-spring z-40" :class="{'lg:ml-64': sidebarOpen}">
            <header class="h-20 bg-white flex items-center justify-between px-4 lg:px-6 border-b">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none p-2 rounded-full hover:bg-gray-100 transition-all duration-300 hover:rotate-90">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800 ml-4">@yield('title', 'Dashboard')</h1>
                </div>
                <div>
                     <div class="flex items-center space-x-2 sm:space-x-4">
                    {{-- Tombol/Ikon untuk ke Halaman Profil --}}
                    <a href="{{ route('admin.profile.edit') }}" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 focus:outline-none transition-all duration-200 hover:scale-110" title="Profile">
                        <svg class="size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </a>

                    {{-- Tombol Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 rounded-full text-gray-500 hover:bg-red-100 hover:text-red-600 focus:outline-none transition-all duration-200 hover:scale-110" title="Logout">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                        </button>
                    </form>
                </div>
            </header>
            
            <main class="flex-1 p-4 lg:p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>