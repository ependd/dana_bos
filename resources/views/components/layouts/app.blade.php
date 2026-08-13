<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50 dark:bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dana BOS' }} - Aplikasi Pengelolaan Dana BOS</title>

    <!-- Favicon / Logo Sekolah -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none; }
    </style>
    <script>
        window.confirmDelete = function(title, text, callback) {
            Swal.fire({
                title: title || 'Konfirmasi Hapus',
                text: text || 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:bg-zinc-900 dark:text-white',
                    title: 'text-lg font-bold text-gray-900 dark:text-white',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md cursor-pointer',
                    cancelButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm cursor-pointer'
                }
            }).then((result) => {
                if (result.isConfirmed && typeof callback === 'function') {
                    callback();
                }
            });
        };

        window.addEventListener('swal:error', event => {
            const data = Array.isArray(event.detail) ? event.detail[0] : event.detail;
            Swal.fire({
                title: data.title || 'Gagal Menghapus',
                text: data.text || 'Tindakan ini tidak dapat diproses.',
                icon: 'error',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-3xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:bg-zinc-900 dark:text-white',
                    title: 'text-lg font-bold text-gray-900 dark:text-white',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md cursor-pointer'
                }
            });
        });
    </script>
</head>
<body class="h-full font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100"
      x-data="{ sidebarOpen: false, userDropdownOpen: false }">

    <div class="min-h-screen bg-gray-50 dark:bg-gray-950">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden" x-cloak></div>

        <!-- Sidebar Navigation (Filament 1:1 Dark/Zinc Style) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-zinc-900 border-r border-zinc-800 text-zinc-300 flex flex-col transition-transform duration-200 ease-in-out">
            
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-zinc-800/80 bg-zinc-950/40">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMP Negeri 40 Kota Bekasi" class="object-contain" style="max-height: 36px; width: auto;">
                    <div>
                        <span class="font-bold text-white tracking-wide text-base block">Dana BOS</span>
                        <span class="text-xs text-zinc-400 block -mt-1 font-medium">Sistem Keuangan</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-zinc-400 hover:text-white p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-6 scrollbar-thin scrollbar-thumb-zinc-700">
                
                @php
                    $user = auth()->user();
                    $currentRoute = request()->route()?->getName();
                @endphp

                <!-- Top Group (Beranda & Kebutuhan) -->
                <div class="space-y-1">
                    <!-- Beranda / Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Beranda</span>
                    </a>

                    @if(!$user?->isKepalaSekolah())
                    <!-- Kebutuhan -->
                    <a href="{{ route('kebutuhan.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('kebutuhan.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('kebutuhan.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Kebutuhan</span>
                    </a>
                    @endif
                </div>

                @if(!$user?->isGuru())
                <!-- Group 1: Anggaran & Realisasi -->
                <div class="space-y-1">
                    <div class="px-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                        Anggaran & Realisasi
                    </div>
                    <div class="mt-2 space-y-1">
                        <!-- RKAS -->
                        <a href="{{ route('rkas.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('rkas.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('rkas.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span>RKAS</span>
                        </a>

                        @if(!$user?->isKepalaSekolah())
                        <!-- BKU -->
                        <a href="{{ route('bku.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('bku.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('bku.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>BKU</span>
                        </a>
                        @endif

                        @if($user?->isKepalaSekolah())
                        <!-- Konfirmasi Barang Datang (Rule 4) -->
                        <a href="{{ route('konfirmasi-barang.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('konfirmasi-barang.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('konfirmasi-barang.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Konfirmasi Barang Datang</span>
                        </a>

                        <!-- Monitoring Anggaran (Gabung ke Anggaran & Realisasi untuk Kepsek) -->
                        <a href="{{ route('monitoring.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('monitoring.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('monitoring.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Monitoring Anggaran</span>
                        </a>
                        @endif
                    </div>
                </div>

                @if(!$user?->isKepalaSekolah())
                <!-- Group 2: Klasifikasi & Monitoring -->
                <div class="space-y-1">
                    <div class="px-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                        {{ $user?->isAdmin() ? 'Klasifikasi' : 'Klasifikasi & Monitoring' }}
                    </div>
                    <div class="mt-2 space-y-1">
                        <!-- Klasifikasi Page -->
                        <a href="{{ route('klasifikasi.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('klasifikasi.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('klasifikasi.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            <span>Klasifikasi</span>
                        </a>

                        @if($user?->isAdmin())
                        <!-- Import Data Training -->
                        <a href="{{ route('import-training.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('import-training.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('import-training.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span>Unggah Data Latih</span>
                        </a>
                        @endif

                        @if(!$user?->isAdmin())
                        <!-- Monitoring Page -->
                        <a href="{{ route('monitoring.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('monitoring.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('monitoring.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Monitoring Anggaran</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                @if($user?->isAdmin())
                <!-- Group 3: Data Master -->
                <div class="space-y-1">
                    <div class="px-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                        Data Master
                    </div>
                    <div class="mt-2 space-y-1">
                        <!-- Katalog Barang -->
                        <a href="{{ route('katalog-barang.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('katalog-barang.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('katalog-barang.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Barang</span>
                        </a>

                        <!-- Kategori Barang -->
                        <a href="{{ route('kategori.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('kategori.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('kategori.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span>Kategori</span>
                        </a>

                        <!-- Periode BOS -->
                        <a href="{{ route('periode-bos.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('periode-bos.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('periode-bos.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Periode BOS</span>
                        </a>
                    </div>
                </div>

                <!-- Bottom Item: Pengguna (Admin only) -->
                <div class="pt-4 border-t border-zinc-800 space-y-1">
                    <a href="{{ route('users.index') }}" 
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('users.*') ? 'bg-blue-600/15 text-blue-400 border border-blue-500/20' : 'hover:bg-zinc-800/70 hover:text-white text-zinc-300' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-blue-400' : 'text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Pengguna</span>
                    </a>
                </div>
                @endif
                @endif

            </nav>

            <!-- Sidebar Footer User Profile with Interactive Flyout to the Right -->
            <div class="p-3 border-t border-zinc-800 bg-zinc-950/90 relative" 
                 x-data="{ userMenuOpen: false }" 
                 @click.outside="userMenuOpen = false">
                
                <button @click="userMenuOpen = !userMenuOpen" 
                        class="w-full flex items-center gap-3 p-1.5 rounded-xl hover:bg-zinc-800 active:bg-zinc-700 transition-all text-left group cursor-pointer">
                    <img class="w-9 h-9 rounded-full object-cover border-2 border-zinc-700 group-hover:border-blue-500 transition-colors shadow-sm" 
                         src="{{ $user?->getFilamentAvatarUrl() ?? 'https://ui-avatars.com/api/?name='.urlencode($user?->name ?? 'User') }}" 
                         alt="{{ $user?->name }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate group-hover:text-blue-400 transition-colors">{{ $user?->name }}</p>
                        <p class="text-[11px] text-zinc-400 capitalize truncate">{{ str_replace('_', ' ', $user?->role ?? 'user') }}</p>
                    </div>
                    <svg class="w-4 h-4 text-zinc-400 group-hover:text-white transition-transform duration-200" 
                         :class="{ 'rotate-90': userMenuOpen }" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <!-- Flyout Menu (Displays to the Right side of the sidebar) -->
                <div x-show="userMenuOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-x-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-x-2 scale-95"
                     class="absolute left-full bottom-0 ml-3 w-64 bg-zinc-900 border border-zinc-700/80 rounded-2xl shadow-2xl overflow-hidden py-1.5 z-[100] divide-y divide-zinc-800">
                    
                    <div class="px-4 py-3 bg-zinc-950/60 flex items-center gap-3">
                        <img class="w-10 h-10 rounded-full object-cover border border-zinc-700" 
                             src="{{ $user?->getFilamentAvatarUrl() }}" 
                             alt="{{ $user?->name }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-white truncate">{{ $user?->name }}</p>
                            <p class="text-[11px] text-zinc-400 truncate mt-0.5">{{ $user?->email }}</p>
                            @if($user?->nip)
                            <p class="text-[10px] text-zinc-500 font-mono mt-0.5">NIP: {{ $user->nip }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="py-1.5">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-zinc-200 hover:bg-zinc-800 hover:text-white transition-colors">
                            <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Edit Profil</span>
                        </a>
                    </div>

                    <div class="py-1.5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-400 hover:bg-rose-950/50 hover:text-rose-300 transition-colors text-left cursor-pointer">
                                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-col min-h-screen" style="margin-left: 0;" id="main-content-wrapper">
        <style>
            @media (min-width: 1024px) {
                #main-content-wrapper {
                    margin-left: 16rem !important; /* 256px = w-64, sidebar width */
                }
            }
        </style>

            <!-- Topbar Header -->
            <header class="sticky top-0 z-30 h-16 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-gray-200 dark:border-zinc-800 flex items-center justify-between px-4 lg:px-8">
                
                <!-- Left: Mobile Toggle & Page Header Title -->
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-zinc-400 dark:hover:text-white dark:hover:bg-zinc-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $header ?? 'Dashboard' }}
                    </h1>
                </div>

                <!-- Right: Blank / Reserved -->
                <div></div>
            </header>

            <!-- Flash Alert Toast Notifications -->
            <div class="px-4 lg:px-8 pt-4">
                @if(session()->has('success'))
                <div x-data="{ show: true }"
                     x-init="setTimeout(() => show = false, 3000)"
                     x-show="show"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:hover:text-emerald-200 text-xs cursor-pointer ml-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @endif

                @if(session()->has('error'))
                <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-8">
                {{ $slot }}
            </main>

        </div>

    </div>

    @livewireScripts
</body>
</html>
