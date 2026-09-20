@props(['role' => 'mahasiswa'])

@php
    $user = auth()->user();
    $initials = strtoupper(substr($user?->name ?? ($role === 'bendahara' ? 'Nadya' : 'Hafizh'), 0, 2));
    $pendingVerificationCount = $role === 'bendahara' 
        ? \App\Models\Payment::where('status', 'pending')->count() 
        : 0;
@endphp

<div>
    <!-- Mobile Drawer Overlay -->
    <div x-show="mobileMenuOpen" 
         x-cloak 
         class="fixed inset-0 z-40 md:hidden bg-stone-900/40 transition-opacity"
         @click="mobileMenuOpen = false"
         aria-hidden="true"></div>

    <!-- Mobile Drawer Panel -->
    <div x-show="mobileMenuOpen" 
         x-cloak 
         class="fixed inset-y-0 left-0 z-50 w-72 max-w-full bg-white border-r border-stone-200 flex flex-col justify-between shadow-xl md:hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
        
        <div>
            <!-- Header with Close Button -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-stone-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-stone-900 flex items-center justify-center text-white font-semibold text-xs">
                        K
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-bold text-stone-900 tracking-tight text-sm">KASMA</span>
                            <span class="text-[9px] uppercase font-bold px-1.5 py-0.2 rounded {{ $role === 'bendahara' ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900' }}">
                                {{ $role === 'bendahara' ? 'Bendahara' : 'Mahasiswa' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-stone-500">Kelas TI-3A</p>
                    </div>
                </div>
                <button type="button" @click="mobileMenuOpen = false" class="p-1.5 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="p-4 space-y-1">
                @if($role === 'mahasiswa')
                <!-- Mahasiswa Menu -->
                <a href="/mahasiswa/dashboard" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-stone-100 text-stone-900">
                    <svg class="w-4 h-4 text-stone-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="/mahasiswa/dashboard#iuran-mingguan" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Iuran Kas</span>
                    </div>
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200/60">Pekan Aktif</span>
                </a>
                <a href="/mahasiswa/dashboard#riwayat-pembayaran" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50">
                    <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Riwayat Pembayaran</span>
                </a>
                <a href="/mahasiswa/dashboard#keuangan-kelas" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50">
                    <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Keuangan Kelas</span>
                </a>
                @else
                <!-- Bendahara Menu -->
                <a href="/bendahara/dashboard" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-stone-100 text-stone-900">
                    <svg class="w-4 h-4 text-stone-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="/bendahara/dashboard#iuran-mingguan" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50">
                    <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Iuran Kas</span>
                </a>
                <a href="/bendahara/dashboard#verifikasi-pembayaran" @click="mobileMenuOpen = false" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Verifikasi Pembayaran</span>
                    </div>
                    @if($pendingVerificationCount > 0)
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200/70">
                            {{ $pendingVerificationCount }} Baru
                        </span>
                    @endif
                </a>
                <a href="/bendahara/dashboard#transaksi-kas" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50">
                    <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Transaksi</span>
                </a>
                <a href="/bendahara/dashboard#mahasiswa-kelas" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50">
                    <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Data Mahasiswa</span>
                </a>
                <a href="/bendahara/dashboard#laporan-keuangan" @click="mobileMenuOpen = false" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50">
                    <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Laporan</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Drawer Footer Profile -->
        <div class="p-4 border-t border-stone-100 bg-stone-50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-stone-200 text-stone-700 flex items-center justify-center font-semibold text-xs border border-stone-300">
                    {{ $initials }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-stone-900 truncate">
                        {{ $user->name ?? ($role === 'bendahara' ? 'Nadya Putri' : 'Hafizh Al-Fatih') }}
                    </p>
                    <p class="text-[11px] text-stone-500">
                        {{ $role === 'bendahara' ? 'Bendahara Kelas' : ('Mahasiswa (' . ($user->nim ?? '220401') . ')') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar (Tailored per role) -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 border-t border-stone-200 py-2 px-4 flex justify-around items-center">
        @if($role === 'mahasiswa')
        <a href="/mahasiswa/dashboard" class="flex flex-col items-center gap-1 text-stone-900">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] font-medium leading-none">Dashboard</span>
        </a>
        <a href="/mahasiswa/dashboard#iuran-mingguan" class="flex flex-col items-center gap-1 text-stone-500 hover:text-stone-900">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-[10px] font-medium leading-none">Iuran</span>
        </a>
        <a href="/mahasiswa/dashboard#riwayat-pembayaran" class="flex flex-col items-center gap-1 text-stone-500 hover:text-stone-900">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-[10px] font-medium leading-none">Riwayat</span>
        </a>
        <a href="/mahasiswa/dashboard#keuangan-kelas" class="flex flex-col items-center gap-1 text-stone-500 hover:text-stone-900">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span class="text-[10px] font-medium leading-none">Keuangan</span>
        </a>
        @else
        <a href="/bendahara/dashboard" class="flex flex-col items-center gap-1 text-stone-900">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] font-medium leading-none">Dashboard</span>
        </a>
        <a href="/bendahara/dashboard#iuran-mingguan" class="flex flex-col items-center gap-1 text-stone-500 hover:text-stone-900">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-[10px] font-medium leading-none">Iuran</span>
        </a>
        <a href="/bendahara/dashboard#verifikasi-pembayaran" class="flex flex-col items-center gap-1 text-stone-500 hover:text-stone-900 relative">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-[10px] font-medium leading-none">Verifikasi</span>
            @if($pendingVerificationCount > 0)
                <span class="absolute top-0 right-3 w-2 h-2 rounded-full bg-amber-500"></span>
            @endif
        </a>
        <a href="/bendahara/dashboard#transaksi-kas" class="flex flex-col items-center gap-1 text-stone-500 hover:text-stone-900">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span class="text-[10px] font-medium leading-none">Transaksi</span>
        </a>
        @endif
    </nav>
</div>
