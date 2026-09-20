@props(['role' => 'mahasiswa'])

<aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 z-30 bg-white border-r border-stone-200/90">
    <!-- Brand / Class Identity -->
    <div class="h-16 flex items-center px-6 border-b border-stone-100">
        <a href="{{ $role === 'bendahara' ? '/bendahara/dashboard' : '/mahasiswa/dashboard' }}" class="flex items-center gap-3 group">
            <div class="w-9 h-9 rounded-lg bg-stone-900 flex items-center justify-center text-white font-semibold text-sm tracking-wide shadow-2xs group-hover:bg-stone-800 transition">
                <span class="font-mono">K</span>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="font-bold text-stone-900 tracking-tight text-base">KASMA</span>
                    <span class="text-[10px] uppercase font-semibold px-1.5 py-0.5 rounded {{ $role === 'bendahara' ? 'bg-amber-50 text-amber-800 border-amber-200/70' : 'bg-emerald-50 text-emerald-800 border-emerald-200/70' }} border">
                        {{ $role === 'bendahara' ? 'Bendahara' : 'Mahasiswa' }}
                    </span>
                </div>
                <p class="text-xs text-stone-500 font-medium leading-none">Kelas TI-3A &bull; Informatika</p>
            </div>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 flex flex-col justify-between px-4 py-6 overflow-y-auto">
        <div class="space-y-6">
            
            @if($role === 'mahasiswa')
            <!-- Mahasiswa Navigation Menu -->
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-2">Menu Mahasiswa</p>
                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <a href="/mahasiswa/dashboard" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ request()->is('mahasiswa/dashboard') && !request()->has('view') ? 'bg-stone-100 text-stone-900' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }} transition">
                        <svg class="w-4 h-4 text-stone-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Iuran Kas -->
                    <a href="{{ route('mahasiswa.iuran.index') }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('mahasiswa.iuran.*') ? 'bg-stone-100 text-stone-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }} transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 {{ request()->routeIs('mahasiswa.iuran.*') ? 'text-stone-700' : 'text-stone-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Iuran Kas</span>
                        </div>
                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200/60">Pekan Aktif</span>
                    </a>

                    <!-- Riwayat Pembayaran -->
                    <a href="/mahasiswa/dashboard#riwayat-pembayaran" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Riwayat Pembayaran</span>
                    </a>

                    <!-- Keuangan Kelas -->
                    <a href="/mahasiswa/dashboard#keuangan-kelas" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Keuangan Kelas</span>
                    </a>
                </nav>
            </div>

            <!-- Student Quick Status Box -->
            @php
                $sidePaid = auth()->check() ? auth()->user()->studentDues()->where('status', 'paid')->count() : 0;
                $sideTotal = \App\Models\CashPeriod::count() ?: 16;
                $sidePct = $sideTotal > 0 ? round(($sidePaid / $sideTotal) * 100, 1) : 0;
                $sideUnpaid = auth()->check() ? auth()->user()->studentDues()->where('status', 'unpaid')->count() : 0;
            @endphp
            <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80 text-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Status Diri</span>
                    <span class="text-[11px] font-semibold {{ $sideUnpaid == 0 ? 'text-emerald-800' : 'text-rose-700' }} font-mono">
                        {{ $sideUnpaid == 0 ? 'Bebas Tunggakan' : $sideUnpaid . ' Pekan Tertunggak' }}
                    </span>
                </div>
                <div class="w-full h-1.5 bg-stone-200 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-700 rounded-full" style="width: {{ min($sidePct, 100) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-stone-500">
                    <span>Lunas: <strong class="text-stone-700">{{ $sidePaid }} Pekan</strong></span>
                    <span>Total: <strong class="text-stone-700">{{ $sideTotal }} Pekan</strong></span>
                </div>
            </div>

            @else
            <!-- Bendahara Navigation Menu -->
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-2">Menu Bendahara</p>
                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <a href="/bendahara/dashboard" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('bendahara.dashboard') ? 'bg-stone-100 text-stone-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }} transition">
                        <svg class="w-4 h-4 {{ request()->routeIs('bendahara.dashboard') ? 'text-stone-700' : 'text-stone-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Iuran Kas -->
                    <a href="{{ route('bendahara.iuran.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('bendahara.iuran.*') ? 'bg-stone-100 text-stone-900 font-semibold' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }} transition">
                        <svg class="w-4 h-4 {{ request()->routeIs('bendahara.iuran.*') ? 'text-stone-700' : 'text-stone-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Iuran Kas</span>
                    </a>

                    <!-- Verifikasi Pembayaran -->
                    @php
                        $sidePendingCount = \App\Models\Payment::where('status', 'pending')->count();
                    @endphp
                    <a href="/bendahara/dashboard#verifikasi-pembayaran" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Verifikasi Pembayaran</span>
                        </div>
                        @if($sidePendingCount > 0)
                            <span class="text-[11px] font-semibold px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200/70">
                                {{ $sidePendingCount }} Baru
                            </span>
                        @endif
                    </a>

                    <!-- Transaksi -->
                    <a href="/bendahara/dashboard#transaksi-kas" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Transaksi</span>
                    </a>

                    <!-- Data Mahasiswa -->
                    @php
                        $sideStuTotal = \App\Models\User::where('role', 'mahasiswa')->where('is_active', true)->count() ?: 32;
                    @endphp
                    <a href="/bendahara/dashboard#mahasiswa-kelas" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>Data Mahasiswa</span>
                        </div>
                        <span class="text-xs text-stone-400 font-mono">{{ $sideStuTotal }}</span>
                    </a>

                    <!-- Laporan -->
                    <a href="/bendahara/dashboard#laporan-keuangan" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-stone-600 hover:bg-stone-50 hover:text-stone-900 transition">
                        <svg class="w-4 h-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Laporan</span>
                    </a>
                </nav>
            </div>

            <!-- Bendahara Milestone Widget -->
            @php
                $sideActivePeriod = \App\Models\CashPeriod::where('is_active', true)->first();
                $sideCollPaid = $sideActivePeriod ? \App\Models\StudentDue::where('cash_period_id', $sideActivePeriod->id)->where('status', 'paid')->count() : 0;
                $sideCollPct = $sideStuTotal > 0 ? round(($sideCollPaid / $sideStuTotal) * 100, 1) : 0;
                $sideCollAmount = $sideActivePeriod ? \App\Models\StudentDue::where('cash_period_id', $sideActivePeriod->id)->where('status', 'paid')->sum('amount') : 0;
                $sideCollTarget = $sideStuTotal * (float) ($sideActivePeriod?->amount ?? 10000);
            @endphp
            <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider">Koleksi {{ $sideActivePeriod->name ?? 'Pekan 9' }}</span>
                    <span class="text-xs font-semibold text-stone-800 font-mono">{{ $sideCollPaid }}/{{ $sideStuTotal }} ({{ $sideCollPct }}%)</span>
                </div>
                <div class="w-full h-1.5 bg-stone-200 rounded-full overflow-hidden">
                    <div class="h-full bg-stone-800 rounded-full" style="width: {{ min($sideCollPct, 100) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-stone-500">
                    <span>Kas Masuk: <strong class="text-stone-700 font-mono">{{ number_format($sideCollAmount / 1000, 0) }}rb</strong></span>
                    <span>Target: <strong class="text-stone-700 font-mono">{{ number_format($sideCollTarget / 1000, 0) }}rb</strong></span>
                </div>
            </div>
            @endif

        </div>

        <!-- User Profile Footer & Logout -->
        <div class="pt-4 border-t border-stone-100 space-y-2">
            <div class="flex items-center justify-between p-2 rounded-lg bg-stone-50/70 border border-stone-200/60">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-stone-200 text-stone-700 flex items-center justify-center font-semibold text-xs border border-stone-300 shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? ($role === 'bendahara' ? 'Nadya' : 'Hafizh'), 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-stone-900 truncate">
                            {{ auth()->user()->name ?? ($role === 'bendahara' ? 'Nadya Putri' : 'Hafizh Al-Fatih') }}
                        </p>
                        <p class="text-[11px] text-stone-500 truncate">
                            @if(auth()->user()?->isMahasiswa() || $role === 'mahasiswa')
                                NIM: {{ auth()->user()->nim ?? '220401' }}
                            @else
                                Bendahara Kelas
                            @endif
                        </p>
                    </div>
                </div>
                <div class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Sesi Aktif"></div>
            </div>

            <!-- Logout Button Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium text-stone-500 hover:text-rose-700 hover:bg-rose-50 transition border border-transparent hover:border-rose-100 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </div>
    </div>
</aside>
