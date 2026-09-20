@props(['role' => 'mahasiswa'])

@php
    $pendingVerificationCount = $role === 'bendahara' 
        ? \App\Models\Payment::where('status', 'pending')->count() 
        : (auth()->check() ? auth()->user()->studentDues()->where('status', 'unpaid')->count() : 0);
@endphp

<header class="sticky top-0 z-20 bg-white border-b border-stone-200/80">
    <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        
        <!-- Left: Mobile Trigger & Page Context -->
        <div class="flex items-center gap-3">
            <button type="button" 
                    @click="mobileMenuOpen = true" 
                    class="md:hidden p-2 -ml-2 rounded-lg text-stone-600 hover:text-stone-900 hover:bg-stone-100 transition cursor-pointer"
                    aria-label="Buka Menu">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base sm:text-lg font-semibold text-stone-900 tracking-tight">
                        {{ $role === 'bendahara' ? 'Dashboard Bendahara' : 'Dashboard Mahasiswa' }}
                    </h1>
                    <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                        Semester Genap 2025/2026
                    </span>
                </div>
                <p class="text-xs text-stone-500 hidden sm:block">
                    {{ $role === 'bendahara' ? 'Kelola kas kelas TI-3A & verifikasi pembayaran' : 'Informasi iuran kas & keuangan kelas TI-3A' }}
                </p>
            </div>
        </div>

        <!-- Right: Role Indicator, Action CTA, and Notifications (NO role switcher) -->
        <div class="flex items-center gap-2.5 sm:gap-3">
            
            <!-- Clean Role Badge -->
            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium {{ $role === 'bendahara' ? 'bg-amber-50 text-amber-900 border border-amber-200/70' : 'bg-emerald-50 text-emerald-900 border border-emerald-200/70' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $role === 'bendahara' ? 'bg-amber-600' : 'bg-emerald-600' }}"></span>
                <span>{{ $role === 'bendahara' ? 'Akses Bendahara' : 'Akses Mahasiswa' }}</span>
            </span>

            @if($role === 'mahasiswa')
            <!-- Mahasiswa CTA: Direct Payment Trigger -->
            <button type="button" 
                    @click="paymentModalOpen = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-emerald-800 hover:bg-emerald-900 transition shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Bayar Kas</span>
            </button>
            @else
            <!-- Bendahara CTA: Record Transaction / Jump to Ledger -->
            <a href="{{ route('bendahara.transaksi.index') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden sm:inline">Lihat Transaksi</span>
                <span class="sm:hidden">Mutasi</span>
            </a>
            @endif

            <!-- Notification Bell -->
            <a href="{{ $role === 'bendahara' ? '/bendahara/dashboard#verifikasi-pembayaran' : '/mahasiswa/dashboard#iuran-mingguan' }}" 
               class="relative p-2 text-stone-500 hover:text-stone-800 hover:bg-stone-100 rounded-lg transition"
               title="{{ $role === 'bendahara' ? ($pendingVerificationCount . ' bukti transfer menunggu verifikasi') : ($pendingVerificationCount . ' iuran kas tertunggak') }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($pendingVerificationCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full {{ $role === 'bendahara' ? 'bg-amber-500' : 'bg-rose-500' }} ring-2 ring-white"></span>
                @endif
            </a>

            <!-- Quick Logout Form -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="p-2 text-stone-400 hover:text-rose-600 hover:bg-stone-100 rounded-lg transition cursor-pointer"
                        title="Keluar dari Akun">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>
