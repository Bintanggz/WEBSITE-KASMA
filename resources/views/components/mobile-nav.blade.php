@props(['role' => 'mahasiswa'])

@php
    $pendingVerificationCount = $role === 'bendahara' 
        ? \App\Models\Payment::where('status', 'pending')->count() 
        : 0;
@endphp

<!-- Mobile Bottom Navigation Bar (Single Primary Mobile Nav) -->
<nav class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md border-t border-zinc-200 py-2 px-1 sm:px-3">
    @if($role === 'mahasiswa')
        <div class="grid grid-cols-5 items-center text-center">
            <!-- Dashboard -->
            <a href="/mahasiswa/dashboard" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('mahasiswa.dashboard') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('mahasiswa.dashboard') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] leading-none">Dashboard</span>
            </a>

            <!-- Iuran -->
            <a href="{{ route('mahasiswa.iuran.index') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('mahasiswa.iuran.*') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('mahasiswa.iuran.*') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[10px] leading-none">Iuran</span>
            </a>

            <!-- Riwayat -->
            <a href="{{ route('mahasiswa.riwayat.index') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('mahasiswa.riwayat.*') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('mahasiswa.riwayat.*') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-[10px] leading-none">Riwayat</span>
            </a>

            <!-- Keuangan -->
            <a href="{{ route('mahasiswa.keuangan.index') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('mahasiswa.keuangan.*') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('mahasiswa.keuangan.*') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] leading-none">Keuangan</span>
            </a>

            <!-- Profil -->
            <a href="{{ route('profile.edit') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('profile.edit') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('profile.edit') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] leading-none">Profil</span>
            </a>
        </div>
    @else
        <div class="grid grid-cols-6 items-center text-center">
            <!-- Dashboard -->
            <a href="/bendahara/dashboard" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('bendahara.dashboard') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('bendahara.dashboard') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] leading-none">Dashboard</span>
            </a>

            <!-- Iuran -->
            <a href="{{ route('bendahara.iuran.index') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('bendahara.iuran.*') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('bendahara.iuran.*') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[10px] leading-none">Iuran</span>
            </a>

            <!-- Verifikasi -->
            <a href="{{ route('bendahara.verifikasi.index') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('bendahara.verifikasi.*') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition relative">
                <div class="relative">
                    <svg class="w-5 h-5 {{ request()->routeIs('bendahara.verifikasi.*') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @if($pendingVerificationCount > 0)
                        <span class="absolute -top-1 -right-1.5 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
                    @endif
                </div>
                <span class="text-[10px] leading-none">Verifikasi</span>
            </a>

            <!-- Transaksi -->
            <a href="{{ route('bendahara.transaksi.index') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('bendahara.transaksi.*') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('bendahara.transaksi.*') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-[10px] leading-none">Transaksi</span>
            </a>

            <!-- Mahasiswa -->
            <a href="{{ route('bendahara.mahasiswa.index') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('bendahara.mahasiswa.*') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('bendahara.mahasiswa.*') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-[10px] leading-none">Siswa</span>
            </a>

            <!-- Laporan -->
            <a href="{{ route('bendahara.laporan.index') }}" 
               class="flex flex-col items-center gap-1 {{ request()->routeIs('bendahara.laporan.*') ? 'text-zinc-900 font-semibold' : 'text-zinc-500 hover:text-zinc-900' }} transition">
                <svg class="w-5 h-5 {{ request()->routeIs('bendahara.laporan.*') ? 'text-zinc-900' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-[10px] leading-none">Laporan</span>
            </a>
        </div>
    @endif
</nav>
