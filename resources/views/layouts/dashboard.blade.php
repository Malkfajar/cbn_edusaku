<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Keuangan' ) - {{ config('app.name', 'Sistem Keuangan') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-main {
            animation: fadeIn 0.5s ease-out forwards;
        }

         .transition-spring {
            transition-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
    </style>

</head>
<body class="bg-gray-100">
    <div  class="relative min-h-screen" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="sidebarOpen = window.innerWidth >= 1024">
        <div  @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden" x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak></div>
        <aside  class="fixed inset-y-0 left-0 bg-[#0b3d91] text-gray-300 w-64 p-6 flex-col z-30 transform transition-transform duration-500 transition-spring" :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            <div class="flex flex-col items-center text-center mb-10">
                <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="h-20 w-20 mb-3 object-contain transition-transform duration-300 hover:rotate-12">
                <span class="text-xl font-bold tracking-wider">CBN EDU</span>
            </div>
            <nav class="flex-1 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-lg text-white hover:bg-[#1e4e9c] transition-all duration-200 transform hover:translate-x-1 {{ request()->routeIs('dashboard') ? 'bg-[#1e4e9c]' : '' }}">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Dashboard</span>
                </a>
                <div x-data="{ open: {{ json_encode(request()->routeIs('payment.*')) }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-white hover:bg-[#1e4e9c] transition-colors duration-200">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span>Pembayaran</span>
                        </div>
                        <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" x-cloak class="pl-12 mt-1 space-y-1">
                        <a href="{{ route('payment.history') }}" class="block py-2 text-sm hover:text-white transition-colors duration-200 {{ request()->routeIs('payment.history') ? 'text-white font-semibold' : 'text-gray-300' }}">Riwayat Pembayaran</a>
                    </div>
                </div>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col transition-all duration-300 ease-in-out" :class="{'lg:ml-64': sidebarOpen}">
            <header class="h-20 bg-white flex items-center justify-between px-4 lg:px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none p-2 rounded-full hover:bg-gray-100 transition-all duration-300 hover:rotate-90">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800 ml-4">@yield('title', 'Sistem Informasi Keuangan')</h1>
                </div>
                
                {{-- PERBAIKAN: Mengubah space-x-5 menjadi space-x-3 dan menghapus div kosong --}}
                <div class="flex items-center space-x-3">
                    <div x-data="notificationCenter()" x-init="init()" class="relative">
                        <button @click="toggle" class="relative text-[#0b3d91] p-2 rounded-full hover:bg-blue-100 transition-all duration-300 transform hover:scale-110">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span x-show="unreadCount > 0" class="absolute top-0 right-0 block h-3 w-3 rounded-full bg-red-500 ring-2 ring-white"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100" 
                             x-transition:enter-start="transform opacity-0 scale-95" 
                             x-transition:enter-end="transform opacity-100 scale-100" 
                             x-transition:leave="transition ease-in duration-75" 
                             x-transition:leave-start="transform opacity-100 scale-100" 
                             x-transition:leave-end="transform opacity-0 scale-95" 
                             class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50" x-cloak>
                            <div class="p-3 border-b flex justify-between items-center">
                                <h3 class="font-semibold text-gray-700">Notifikasi</h3>
                                <button @click="markAllAsRead" class="text-sm text-blue-600 hover:underline" :disabled="unreadCount === 0">Tandai semua dibaca</button>
                            </div>
                            <div class="py-1 max-h-96 overflow-y-auto">
                                <template x-if="notifications.length > 0">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <a :href="notification.url || '#'" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100" :class="!notification.read_at ? 'bg-blue-50' : ''">
                                            <p class="font-bold" x-text="notification.title"></p>
                                            <p class="text-xs" x-text="notification.message"></p>
                                            <p class="text-xs text-gray-500 mt-1" x-text="timeAgo(notification.created_at)"></p>
                                        </a>
                                    </template>
                                </template>
                                <template x-if="notifications.length === 0">
                                    <p class="text-center text-gray-500 py-4">Tidak ada notifikasi.</p>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-[#0b3d91] p-2 rounded-full hover:bg-blue-100 focus:outline-none transition-all duration-300 transform hover:scale-110">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5" x-cloak">
                            <div class="px-4 py-3">
                                <p class="text-sm text-gray-900">Masuk sebagai</p>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            </div>
                            <div class="border-t border-gray-100"></div>
                            <a href="{{ route('profile.index') }}" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('profile.index') ? 'bg-gray-100' : '' }}">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>My Profile</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 p-4 lg:p-6 overflow-y-auto animate-fade-in-main">
                @yield('content')
            </main>
        </div>
    </div>
    
@stack('scripts')

<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>

@stack('scripts')

<script>
    // Fungsi untuk mengubah format waktu (contoh sederhana)
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " tahun lalu";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " bulan lalu";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " hari lalu";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " jam lalu";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " menit lalu";
        return Math.floor(seconds) + " detik lalu";
    }

    function notificationCenter() {
        return {
            open: false,
            notifications: [],
            unreadCount: 0,
            
            init() {
                this.fetchUnreadCount();
                
                // Menangani Notifikasi Foreground (saat user sedang membuka halaman)
                const messaging = firebase.messaging();
                messaging.onMessage((payload) => {
                    console.log('Notifikasi diterima saat halaman aktif:', payload);
                    // Tambah hitungan & muat ulang notifikasi
                    this.unreadCount++;
                    this.fetchNotifications();
                });
            },

            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.fetchNotifications();
                }
            },

            async fetchUnreadCount() {
                const response = await fetch('{{ route("api.notifications.unread-count") }}');
                const data = await response.json();
                this.unreadCount = data.count;
            },

            async fetchNotifications() {
                const response = await fetch('{{ route("api.notifications.index") }}');
                this.notifications = await response.json();
            },

            async markAllAsRead() {
                if(this.unreadCount === 0) return;

                await fetch('{{ route("api.notifications.mark-all-as-read") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                // Perbarui UI secara langsung
                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                this.unreadCount = 0;
            }
        }
    }
</script>

</body>
</html>