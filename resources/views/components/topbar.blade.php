@props([
    'role' => 'mahasiswa',
    'title' => null,
    'subtitle' => null,
])

@php
    $pendingVerificationCount = $role === 'bendahara' 
        ? \App\Models\Payment::where('status', 'pending')->count() 
        : (auth()->check() ? auth()->user()->studentDues()->where('status', 'unpaid')->count() : 0);

    $displayTitle = $title ?? ($role === 'bendahara' ? 'Dashboard Bendahara' : 'Dashboard Mahasiswa');
    $displaySubtitle = $subtitle ?? ($role === 'bendahara' ? 'Kelola kas kelas TI26A3 & verifikasi pembayaran' : 'Informasi iuran kas & keuangan kelas TI26A3');
@endphp

<header class="sticky top-0 z-20 bg-white/95 backdrop-blur-xs border-b border-zinc-200">
    <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        
        <!-- Left: Page Context & Title -->
        <div class="flex items-center gap-3 min-w-0">

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h1 class="text-base sm:text-lg font-semibold text-zinc-900 tracking-tight truncate">
                        {{ $displayTitle }}
                    </h1>
                    @php
                        $activePeriodForTopbar = \App\Models\CashPeriod::where('is_active', true)->first();
                    @endphp
                    @if($activePeriodForTopbar)
                    <span class="hidden lg:inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-600 border border-zinc-200 shrink-0">
                        {{ $activePeriodForTopbar->academic_year }} &bull; Smt {{ ucfirst($activePeriodForTopbar->semester) }}
                    </span>
                    @endif
                </div>
                <p class="text-xs text-zinc-500 hidden sm:block truncate">
                    {{ $displaySubtitle }}
                </p>
            </div>
        </div>

        <!-- Right: Role Indicator, Action CTA, and Notifications -->
        <div class="flex items-center gap-2 sm:gap-2.5">
            
            <!-- Clean Subtle Role Badge -->
            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium {{ $role === 'bendahara' ? 'bg-amber-50 text-amber-900 border border-amber-200/80' : 'bg-emerald-50 text-emerald-900 border border-emerald-200/80' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $role === 'bendahara' ? 'bg-amber-600' : 'bg-emerald-600' }}"></span>
                <span>{{ $role === 'bendahara' ? 'Bendahara' : 'Mahasiswa' }}</span>
            </span>

            @if($role === 'mahasiswa')
            <!-- Mahasiswa CTA: Direct Payment Trigger -->
            <button type="button" 
                    @click="paymentModalOpen = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Bayar Kas</span>
            </button>
            @else
            <!-- Bendahara CTA: Jump to Ledger -->
            <a href="{{ route('bendahara.transaksi.index') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden sm:inline">Mutasi Kas</span>
                <span class="sm:hidden">Mutasi</span>
            </a>
            @endif

            <!-- Notification Bell -->
            <a href="{{ $role === 'bendahara' ? route('bendahara.verifikasi.index') : route('mahasiswa.iuran.index') }}" 
               class="relative p-2 text-zinc-500 hover:text-zinc-800 hover:bg-zinc-100 rounded-lg transition"
               title="{{ $role === 'bendahara' ? ($pendingVerificationCount . ' bukti transfer menunggu verifikasi') : ($pendingVerificationCount . ' iuran kas tertunggak') }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($pendingVerificationCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full {{ $role === 'bendahara' ? 'bg-amber-500' : 'bg-rose-500' }} ring-2 ring-white"></span>
                @endif
            </a>

            <!-- User Profile Link -->
            <a href="{{ route('profile.edit') }}" 
               class="p-2 text-zinc-500 hover:text-zinc-800 hover:bg-zinc-100 rounded-lg transition"
               title="Pengaturan Profil & Sandi">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </a>

            <!-- Quick Logout Form -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="p-2 text-zinc-400 hover:text-rose-600 hover:bg-zinc-100 rounded-lg transition cursor-pointer"
                        title="Keluar dari Akun">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>
