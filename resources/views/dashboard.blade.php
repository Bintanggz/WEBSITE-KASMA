<x-layouts.app title="Ringkasan Kas">

    <!-- Header & Context Banner -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-stone-200">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-stone-900 tracking-tight">
                        <span x-show="currentRole === 'treasurer'">Ringkasan Kas &bull; Panel Bendahara</span>
                        <span x-show="currentRole === 'student'">Buku Kas &bull; Panel Mahasiswa</span>
                    </h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                        Pekan ke-9 Aktif
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                    <span x-show="currentRole === 'treasurer'">Kelola setoran mingguan, verifikasi bukti transfer, dan pantau pengeluaran kelas.</span>
                    <span x-show="currentRole === 'student'">Pantau transparansi kas kelas TI26A3 dan status pembayaran iuran mingguan Anda.</span>
                </p>
            </div>

            <!-- Class Quick Metadata Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-stone-200 text-stone-600 text-xs shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    <span>Iuran: <strong class="text-stone-900 font-mono">Rp 10.000</strong>/mgg</span>
                </div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-stone-200 text-stone-600 text-xs shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-stone-400"></span>
                    <span>Mahasiswa: <strong class="text-stone-900 font-mono">32 Org</strong></span>
                </div>
            </div>
        </div>

        <!-- Pending Verification Notice (Treasurer View) -->
        <div x-show="currentRole === 'treasurer'" class="mt-3 p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Ada <strong>3 setoran transfer baru</strong> (total Rp 60.000) yang menunggu pengecekan dan verifikasi bukti pembayaran.</span>
            </div>
            <a href="#verifikasi" class="font-semibold underline hover:text-amber-950 shrink-0 ml-2">Periksa Sekarang &rarr;</a>
        </div>

        <!-- Payment Reminder Notice (Student View) -->
        <div x-show="currentRole === 'student'" class="mt-3 p-3 rounded-lg bg-emerald-50/70 border border-emerald-200 text-emerald-900 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Status kas Anda: <strong>Lunas sampai Pekan 9</strong>. Terima kasih telah tertib membayar kas tepat waktu!</span>
            </div>
            <span class="font-mono text-emerald-800 font-semibold shrink-0 ml-2">Bebas Tunggakan</span>
        </div>
    </div>

    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <!-- Card 1: Saldo Kas Aktif -->
        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs hover:border-stone-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Sisa Saldo Kas</span>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                    Aktif
                </span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 tracking-tight">Rp 3.700.000</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>Rekening BCA Kelas</span>
                <span class="font-mono text-stone-700">873-019-2819</span>
            </div>
        </div>

        <!-- Card 2: Total Pemasukan -->
        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs hover:border-stone-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Total Pemasukan</span>
                <span class="text-[11px] font-mono text-emerald-700 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded">
                    +Rp 120.000 pkn ini
                </span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 tracking-tight">Rp 4.850.000</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>485 Setoran Iuran</span>
                <span class="text-stone-700 font-medium">9 Pekan Berjalan</span>
            </div>
        </div>

        <!-- Card 3: Total Pengeluaran -->
        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs hover:border-stone-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Total Pengeluaran</span>
                <span class="text-[11px] text-stone-500">5 Kegiatan</span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 tracking-tight">Rp 1.150.000</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>Rincian Nota Lengkap</span>
                <span class="text-stone-700 font-medium">Transparan</span>
            </div>
        </div>

        <!-- Card 4: Role-adaptive Status Card -->
        <!-- Treasurer: Menunggu Verifikasi -->
        <div x-show="currentRole === 'treasurer'" class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs hover:border-stone-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Perlu Verifikasi</span>
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-amber-900 tracking-tight">3 Setoran</span>
                <span class="text-xs text-stone-500 font-mono">(Rp 60.000)</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>Bukti Transfer Masuk</span>
                <a href="#verifikasi" class="text-amber-800 font-semibold hover:underline">Tinjau &rarr;</a>
            </div>
        </div>

        <!-- Student: Iuran Pribadi -->
        <div x-show="currentRole === 'student'" class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs hover:border-stone-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Iuran Saya</span>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                    Lunas
                </span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-emerald-900 tracking-tight">9 / 16 Pekan</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>Total Disetor: Rp 90.000</span>
                <span class="text-emerald-800 font-medium">Tertib</span>
            </div>
        </div>

    </div>

    <!-- Weekly Cash Status (Matrix Pekan 1 - 16) -->
    <div id="iuran" class="mb-8 bg-white p-5 sm:p-6 rounded-xl border border-stone-200/90 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 mb-4 border-b border-stone-100">
            <div>
                <h3 class="font-semibold text-stone-900 text-sm sm:text-base tracking-tight">Status Kas Mingguan (Semester Genap 2025/2026)</h3>
                <p class="text-xs text-stone-500 mt-0.5">
                    <span x-show="currentRole === 'treasurer'">Progres pelunasan iuran 32 mahasiswa per pekan perkuliahan.</span>
                    <span x-show="currentRole === 'student'">Riwayat pelunasan iuran kas pribadi Anda per pekan perkuliahan.</span>
                </p>
            </div>

            <!-- Legend Indicator -->
            <div class="flex flex-wrap items-center gap-3 text-xs text-stone-600">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-emerald-600"></span>
                    <span class="text-[11px]">Lunas</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-amber-400"></span>
                    <span class="text-[11px]">Pekan Berjalan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-sm bg-stone-200"></span>
                    <span class="text-[11px]">Mendatang</span>
                </div>
            </div>
        </div>

        <!-- 16 Weeks Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-2.5">
            
            <!-- Week 1 - 8 (Completed) -->
            @for ($i = 1; $i <= 8; $i++)
            <div class="p-3 rounded-lg border border-emerald-200 bg-emerald-50/50 flex flex-col justify-between transition hover:border-emerald-300">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-950 font-mono">Pekan {{ $i }}</span>
                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="mt-3">
                    <div x-show="currentRole === 'treasurer'" class="text-[11px] text-emerald-800">
                        <p class="font-mono font-semibold">32/32</p>
                        <p class="text-[10px] text-emerald-700/80">100% Lunas</p>
                    </div>
                    <div x-show="currentRole === 'student'" class="text-[11px] text-emerald-800">
                        <p class="font-semibold text-[11px]">Lunas</p>
                        <p class="text-[10px] font-mono text-emerald-700/80">Rp 10.000</p>
                    </div>
                </div>
            </div>
            @endfor

            <!-- Week 9 (Current Active Week) -->
            <div class="p-3 rounded-lg border-2 border-stone-800 bg-stone-50 flex flex-col justify-between shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-stone-900 font-mono">Pekan 9</span>
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                </div>
                <div class="mt-3">
                    <div x-show="currentRole === 'treasurer'" class="text-[11px] text-stone-800">
                        <p class="font-mono font-bold text-stone-900">28/32</p>
                        <p class="text-[10px] font-medium text-amber-800">87.5% Terkumpul</p>
                    </div>
                    <div x-show="currentRole === 'student'" class="text-[11px] text-stone-800">
                        <p class="font-bold text-emerald-700">Lunas</p>
                        <p class="text-[10px] text-stone-500">Terverifikasi</p>
                    </div>
                </div>
            </div>

            <!-- Weeks 10 - 16 (Upcoming Weeks) -->
            @for ($i = 10; $i <= 16; $i++)
            <div class="p-3 rounded-lg border border-stone-200/70 bg-stone-50/50 flex flex-col justify-between text-stone-400">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium font-mono text-stone-500">Pekan {{ $i }}</span>
                    <span class="text-[10px] text-stone-400">&bull;</span>
                </div>
                <div class="mt-3">
                    <p class="text-[11px] text-stone-400 font-medium">Mendatang</p>
                    <p class="text-[10px] font-mono text-stone-400">Rp 10.000</p>
                </div>
            </div>
            @endfor

        </div>

        <!-- Weekly Progress Bar Info -->
        <div class="mt-4 pt-3 border-t border-stone-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-stone-500">
            <div class="flex items-center gap-2">
                <span>Pekan ke-9 Aktif:</span>
                <div class="w-36 h-2 bg-stone-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-600 rounded-full" style="width: 87.5%"></div>
                </div>
                <span class="font-mono font-semibold text-stone-700">28 dari 32 Mahasiswa Lunas</span>
            </div>
            <div class="text-[11px]">
                Jatuh tempo iuran pekan ini: <strong>Jumat, 18 September 2026</strong>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Section: Transactions & Verification/Payment Action -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Transactions Ledger (lg:col-span-8) -->
        <div id="transaksi" class="lg:col-span-8 space-y-6">
            
            <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
                <!-- Card Header with Filter Pills -->
                <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-stone-900 text-sm sm:text-base tracking-tight">Catatan Transaksi Terkini</h3>
                        <p class="text-xs text-stone-500">Buku kas kasir transparan &bull; Pemasukan & Pengeluaran kelas</p>
                    </div>

                    <!-- Filter Pills -->
                    <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-lg border border-stone-200/80 text-xs">
                        <button type="button" 
                                @click="activeTab = 'semua'" 
                                :class="activeTab === 'semua' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                class="px-2.5 py-1 rounded-md transition text-xs">
                            Semua (6)
                        </button>
                        <button type="button" 
                                @click="activeTab = 'masuk'" 
                                :class="activeTab === 'masuk' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                class="px-2.5 py-1 rounded-md transition text-xs">
                            Pemasukan
                        </button>
                        <button type="button" 
                                @click="activeTab = 'keluar'" 
                                :class="activeTab === 'keluar' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                class="px-2.5 py-1 rounded-md transition text-xs">
                            Pengeluaran
                        </button>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Keperluan & Keterangan</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4 text-right">Nominal</th>
                                <th class="py-3 px-4 text-center">Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            
                            <!-- Row 1: Pemasukan -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="activeTab === 'semua' || activeTab === 'masuk'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    15 Sep 2026
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Setoran Iuran Kas Pekan 9 (Gelombang 2)</div>
                                    <div class="text-[11px] text-stone-400">Penyetoran 6 mahasiswa &bull; Verifikasi Bendahara 1</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        Iuran Kas
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-800 whitespace-nowrap">
                                    +Rp 60.000
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Setoran Gelombang 2', amount: 'Rp 60.000', method: 'Transfer QRIS/BCA', time: '15 Sep 2026, 14:20', weeks: 'Pekan 9'})"
                                            class="inline-flex items-center gap-1 text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2 py-1 rounded transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 2: Pengeluaran -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="activeTab === 'semua' || activeTab === 'keluar'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    12 Sep 2026
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Pembelian Spidol & Penghapus Whiteboard Kelas</div>
                                    <div class="text-[11px] text-stone-400">PJ: Seksi Perlengkapan (Bagas) &bull; Ruang Lab TI-302</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 text-stone-700 border border-stone-200">
                                        Perlengkapan
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-rose-800 whitespace-nowrap">
                                    -Rp 45.000
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Nota Toko Buku Karunia', amount: 'Rp 45.000', method: 'Cash / Reimburse', time: '12 Sep 2026, 10:15', weeks: 'Perlengkapan'})"
                                            class="inline-flex items-center gap-1 text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2 py-1 rounded transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Nota</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 3: Pemasukan -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="activeTab === 'semua' || activeTab === 'masuk'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    10 Sep 2026
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Setoran Iuran Kas Pekan 9 (Gelombang 1)</div>
                                    <div class="text-[11px] text-stone-400">Penyetoran 22 mahasiswa lunas langsung</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        Iuran Kas
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-800 whitespace-nowrap">
                                    +Rp 220.000
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Rekap Rekening Bank BCA', amount: 'Rp 220.000', method: 'BCA Virtual / QRIS', time: '10 Sep 2026, 17:00', weeks: 'Pekan 9'})"
                                            class="inline-flex items-center gap-1 text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2 py-1 rounded transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 4: Pengeluaran Dana Sosial -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="activeTab === 'semua' || activeTab === 'keluar'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    05 Sep 2026
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Dana Sosial: Menjenguk Rekan Kelas Sakit (Rian)</div>
                                    <div class="text-[11px] text-stone-400">Pembelian buah tangan & parsel RSUD</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200/60">
                                        Dana Sosial
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-rose-800 whitespace-nowrap">
                                    -Rp 150.000
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Kuitansi Toko Buah Segar', amount: 'Rp 150.000', method: 'Cash', time: '05 Sep 2026, 16:30', weeks: 'Dana Sosial'})"
                                            class="inline-flex items-center gap-1 text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2 py-1 rounded transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Nota</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 5: Pengeluaran Modul Praktikum -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="activeTab === 'semua' || activeTab === 'keluar'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    01 Sep 2026
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Fotokopi Modul Praktikum Basis Data (32 Eksemplar)</div>
                                    <div class="text-[11px] text-stone-400">PJ: Seksi Akademik &bull; Percetakan Kampus</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 text-stone-700 border border-stone-200">
                                        Akademik
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-rose-800 whitespace-nowrap">
                                    -Rp 280.000
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Nota Percetakan Kampus Jaya', amount: 'Rp 280.000', method: 'Transfer BCA', time: '01 Sep 2026, 11:00', weeks: 'Modul Praktikum'})"
                                            class="inline-flex items-center gap-1 text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2 py-1 rounded transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Nota</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 6: Pemasukan Pekan 8 -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="activeTab === 'semua' || activeTab === 'masuk'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    28 Agu 2026
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Rekap Pelunasan Kas Pekan 8 (32 Mahasiswa)</div>
                                    <div class="text-[11px] text-stone-400">Semua mahasiswa lunas 100% tepat waktu</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        Iuran Kas
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-800 whitespace-nowrap">
                                    +Rp 320.000
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Rekap Kas Pekan 8', amount: 'Rp 320.000', method: 'Sistem Kasma', time: '28 Agu 2026, 18:00', weeks: 'Pekan 8'})"
                                            class="inline-flex items-center gap-1 text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2 py-1 rounded transition">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Lihat</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer of table -->
                <div class="p-3 bg-stone-50 border-t border-stone-100 text-center">
                    <a href="#transaksi" class="text-xs font-semibold text-stone-600 hover:text-stone-900">
                        Lihat Seluruh 48 Transaksi Kas Kelas &rarr;
                    </a>
                </div>
            </div>

            <!-- Financial Allocation Breakdown Card (Class Transparency) -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
                    <div>
                        <h4 class="font-semibold text-stone-900 text-sm">Transparansi Alokasi Kas Kelas</h4>
                        <p class="text-xs text-stone-500">Berdasarkan kesepakatan musyawarah kelas semester ini</p>
                    </div>
                    <span class="text-xs font-mono text-stone-500">Anggaran 2026</span>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-stone-700">Kegiatan Kelas & Buka Bersama (40%)</span>
                            <span class="font-mono text-stone-600">Rp 460.000 / Rp 2.048.000</span>
                        </div>
                        <div class="w-full h-1.5 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full bg-stone-700 rounded-full" style="width: 22%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-stone-700">Fotokopi Modul & Perlengkapan Belajar (35%)</span>
                            <span class="font-mono text-stone-600">Rp 325.000 / Rp 1.792.000</span>
                        </div>
                        <div class="w-full h-1.5 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-700 rounded-full" style="width: 18%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-stone-700">Dana Sosial & Tanggap Darurat (25%)</span>
                            <span class="font-mono text-stone-600">Rp 150.000 / Rp 1.280.000</span>
                        </div>
                        <div class="w-full h-1.5 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-600 rounded-full" style="width: 12%"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Verification Queue (Treasurer) OR Payment Status (Student) (lg:col-span-4) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- TREASURER VIEW: Antrean Verifikasi Pembayaran -->
            <div id="verifikasi" x-show="currentRole === 'treasurer'" class="bg-white rounded-xl border border-stone-200/90 shadow-2xs p-5">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-stone-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h3 class="font-semibold text-stone-900 text-sm">Verifikasi Masuk</h3>
                    </div>
                    <span class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200/60">
                        3 Menunggu
                    </span>
                </div>

                <div class="space-y-3">
                    
                    <!-- Item 1 -->
                    <div class="p-3 rounded-lg border border-stone-200 bg-stone-50/60 space-y-2.5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-stone-900 text-xs">Farhan Pratama</h4>
                                <p class="text-[11px] font-mono text-stone-500">NIM: 220412 &bull; QRIS</p>
                            </div>
                            <span class="text-xs font-bold font-mono text-stone-900">Rp 20.000</span>
                        </div>
                        <div class="text-[11px] text-stone-600 flex items-center justify-between">
                            <span>Untuk: <strong>Pekan 9 & 10</strong></span>
                            <span class="text-stone-400">10 mnt lalu</span>
                        </div>
                        <div class="flex gap-1.5 pt-1">
                            <button type="button" 
                                    @click="openProof({name: 'Farhan Pratama', amount: 'Rp 20.000', method: 'Transfer QRIS Statis', time: '16 Sep 2026, 17:50', weeks: 'Pekan 9 dan Pekan 10'})" 
                                    class="flex-1 py-1 px-2 text-[11px] font-medium rounded bg-stone-200/80 hover:bg-stone-300 text-stone-800 transition text-center">
                                Cek Bukti
                            </button>
                            <button type="button" class="py-1 px-2.5 text-[11px] font-medium rounded bg-emerald-700 hover:bg-emerald-800 text-white transition">
                                Setujui
                            </button>
                            <button type="button" class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-600 transition">
                                Tolak
                            </button>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="p-3 rounded-lg border border-stone-200 bg-stone-50/60 space-y-2.5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-stone-900 text-xs">Siti Nurhaliza</h4>
                                <p class="text-[11px] font-mono text-stone-500">NIM: 220425 &bull; BCA Mobile</p>
                            </div>
                            <span class="text-xs font-bold font-mono text-stone-900">Rp 10.000</span>
                        </div>
                        <div class="text-[11px] text-stone-600 flex items-center justify-between">
                            <span>Untuk: <strong>Pekan 9</strong></span>
                            <span class="text-stone-400">35 mnt lalu</span>
                        </div>
                        <div class="flex gap-1.5 pt-1">
                            <button type="button" 
                                    @click="openProof({name: 'Siti Nurhaliza', amount: 'Rp 10.000', method: 'Transfer Antar Rekening BCA', time: '16 Sep 2026, 17:25', weeks: 'Pekan 9'})" 
                                    class="flex-1 py-1 px-2 text-[11px] font-medium rounded bg-stone-200/80 hover:bg-stone-300 text-stone-800 transition text-center">
                                Cek Bukti
                            </button>
                            <button type="button" class="py-1 px-2.5 text-[11px] font-medium rounded bg-emerald-700 hover:bg-emerald-800 text-white transition">
                                Setujui
                            </button>
                            <button type="button" class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-600 transition">
                                Tolak
                            </button>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="p-3 rounded-lg border border-stone-200 bg-stone-50/60 space-y-2.5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-stone-900 text-xs">Dimas Arya</h4>
                                <p class="text-[11px] font-mono text-stone-500">NIM: 220408 &bull; Mandiri Livin</p>
                            </div>
                            <span class="text-xs font-bold font-mono text-stone-900">Rp 30.000</span>
                        </div>
                        <div class="text-[11px] text-stone-600 flex items-center justify-between">
                            <span>Untuk: <strong>Pekan 9, 10, 11</strong></span>
                            <span class="text-stone-400">1 jam lalu</span>
                        </div>
                        <div class="flex gap-1.5 pt-1">
                            <button type="button" 
                                    @click="openProof({name: 'Dimas Arya', amount: 'Rp 30.000', method: 'Transfer BI-FAST Mandiri', time: '16 Sep 2026, 16:40', weeks: 'Pekan 9, 10, 11'})" 
                                    class="flex-1 py-1 px-2 text-[11px] font-medium rounded bg-stone-200/80 hover:bg-stone-300 text-stone-800 transition text-center">
                                Cek Bukti
                            </button>
                            <button type="button" class="py-1 px-2.5 text-[11px] font-medium rounded bg-emerald-700 hover:bg-emerald-800 text-white transition">
                                Setujui
                            </button>
                            <button type="button" class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-600 transition">
                                Tolak
                            </button>
                        </div>
                    </div>

                </div>

                <div class="mt-4 pt-3 border-t border-stone-100 text-center">
                    <span class="text-[11px] text-stone-400">Verifikasi teliti sesuai mutasi rekening resmi.</span>
                </div>
            </div>

            <!-- STUDENT VIEW: Kartu Iuran Digital Pribadi -->
            <div x-show="currentRole === 'student'" class="bg-white rounded-xl border border-stone-200/90 shadow-2xs p-5">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-stone-100">
                    <div>
                        <h3 class="font-semibold text-stone-900 text-sm">Kartu Iuran Mahasiswa</h3>
                        <p class="text-xs text-stone-500">Hafizh Al-Fatih &bull; NIM 220401</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                        Tertib
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-lg bg-stone-50 border border-stone-200 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-stone-500">Status Pekan Ini (Pekan 9):</span>
                            <span class="font-semibold text-emerald-700">Lunas</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Total Disetor (Pekan 1 - 9):</span>
                            <span class="font-semibold font-mono text-stone-900">Rp 90.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Sisa Tanggungan Semester:</span>
                            <span class="font-semibold font-mono text-stone-900">7 Pekan (Rp 70.000)</span>
                        </div>
                    </div>

                    <!-- Payment Information Box -->
                    <div class="p-3 rounded-lg bg-emerald-50/50 border border-emerald-200/80 space-y-2 text-emerald-950">
                        <div class="flex items-center gap-1.5 font-semibold text-xs text-emerald-900">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>Rekening Resmi Kas TI26A3</span>
                        </div>
                        <div class="bg-white p-2.5 rounded border border-emerald-200 text-[11px] font-mono flex items-center justify-between">
                            <div>
                                <span class="text-stone-500 block text-[10px]">Bank Central Asia (BCA)</span>
                                <span class="font-bold text-stone-900 text-xs">873-019-2819</span>
                                <span class="text-stone-500 block text-[10px]">a.n. Bendahara Kas TI26A3</span>
                            </div>
                            <span class="text-[10px] text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded font-sans font-medium">Salin</span>
                        </div>
                    </div>

                    <!-- Upload Proof Button -->
                    <button type="button" 
                            class="w-full py-2.5 px-3 rounded-lg bg-emerald-800 hover:bg-emerald-900 text-white font-medium text-xs shadow-xs transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Kirim Bukti Pembayaran Kas</span>
                    </button>
                </div>
            </div>

            <!-- Fast Contact Bendahara Card -->
            <div class="p-4 rounded-xl bg-white border border-stone-200/90 shadow-2xs">
                <h4 class="font-semibold text-stone-900 text-xs mb-1">Kontak Bendahara Kelas</h4>
                <p class="text-[11px] text-stone-500 mb-3">Ada kendala pembayaran atau butuh konfirmasi dispensasi kas?</p>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-stone-50 border border-stone-100">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-stone-200 text-[10px] font-bold text-stone-700 flex items-center justify-center">1</div>
                            <div>
                                <p class="font-medium text-stone-900 text-[11px]">Hafizh (Bendahara 1)</p>
                                <p class="text-[10px] text-stone-500 font-mono">0812-3456-7890</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded">WhatsApp</span>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-lg bg-stone-50 border border-stone-100">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-stone-200 text-[10px] font-bold text-stone-700 flex items-center justify-center">2</div>
                            <div>
                                <p class="font-medium text-stone-900 text-[11px]">Nadya (Bendahara 2)</p>
                                <p class="text-[10px] text-stone-500 font-mono">0821-9876-5432</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded">WhatsApp</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
