<x-layouts.app role="bendahara" title="Dashboard Bendahara">

    <!-- Header & Context Banner -->
    <div class="mb-6 pb-4 border-b border-stone-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-stone-900 tracking-tight">Panel Pengelolaan Kas &bull; Bendahara</h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-900 border border-amber-200/70">
                        Pekan ke-9 Aktif
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                    Kelas TI-3A &bull; Pengelola: <span class="font-medium text-stone-700">Nadya Putri (Bendahara 1)</span>
                </p>
            </div>

            <!-- Focus 6: Quick Actions Header CTAs -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Catat Pengeluaran</span>
                </button>
                <button type="button" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-stone-700 bg-white border border-stone-200 hover:bg-stone-50 transition shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Catat Setoran Tunai</span>
                </button>
            </div>
        </div>

        <!-- Pending Alert Notice -->
        <div class="mt-3 p-3 rounded-lg bg-amber-50/80 border border-amber-200 text-amber-950 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Ada <strong>3 bukti transfer masuk</strong> (Rp 60.000) yang perlu dicek dan disetujui.</span>
            </div>
            <a href="#verifikasi-pembayaran" class="font-semibold underline hover:text-amber-900 shrink-0 ml-2">Tinjau Sekarang &rarr;</a>
        </div>
    </div>

    <!-- Top Focus Metrics: 1. Current Balance & 2. Weekly Collection -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <!-- Focus 1: Current Cash Balance -->
        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Saldo Kas Kelas Saat Ini</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">Aktif</span>
            </div>
            <div class="mt-2.5">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900">Rp 3.700.000</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>Rekening BCA Kas</span>
                <span class="font-mono text-stone-700 font-medium">873-019-2819</span>
            </div>
        </div>

        <!-- Focus 2: Weekly Collection (Pekan 9) -->
        <div id="iuran-mingguan" class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Iuran Pekan 9 Terkumpul</span>
                <span class="text-[11px] font-mono text-stone-700 font-semibold bg-stone-100 px-1.5 py-0.5 rounded">87.5%</span>
            </div>
            <div class="mt-2.5">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900">Rp 280.000</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>28 dari 32 Mahasiswa Lunas</span>
                <span class="text-stone-700 font-mono">Target: 320rb</span>
            </div>
        </div>

        <!-- Focus 3 Metric: Pending Payments -->
        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Menunggu Verifikasi</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="mt-2.5 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-amber-900">3 Bukti</span>
                <span class="text-xs text-stone-500 font-mono">(Rp 60.000)</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>Setoran Transfer Siswa</span>
                <a href="#verifikasi-pembayaran" class="text-amber-800 font-semibold hover:underline">Periksa &rarr;</a>
            </div>
        </div>

        <!-- Focus 4 Metric: Unpaid Students Count -->
        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Belum Bayar Pekan Ini</span>
                <span class="text-[10px] font-semibold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">4 Mahasiswa</span>
            </div>
            <div class="mt-2.5">
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900">4 Orang</span>
            </div>
            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                <span>Tunggakan: Rp 40.000</span>
                <a href="#mahasiswa-belum-bayar" class="text-stone-700 font-semibold hover:underline">Lihat Nama &rarr;</a>
            </div>
        </div>

    </div>

    <!-- Main Grid: Left Column (Transactions & Unpaid Students) & Right Column (Verification Queue & Quick Actions) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Column: Transactions & Unpaid Students (lg:col-span-7) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Focus 4: Unpaid Students List (Daftar Mahasiswa Belum Bayar Pekan 9) -->
            <div id="mahasiswa-belum-bayar" class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <h3 class="font-semibold text-stone-900 text-base tracking-tight">Mahasiswa Belum Bayar (Pekan ke-9)</h3>
                        </div>
                        <p class="text-xs text-stone-500 mt-0.5">4 mahasiswa belum melunasi kas sebelum jatuh tempo Jumat, 18 Sep 2026</p>
                    </div>
                    <button type="button" 
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition self-start sm:self-auto">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Ingatkan Semua via WA</span>
                    </button>
                </div>

                <div class="divide-y divide-stone-100 text-xs">
                    <!-- Student 1 -->
                    <div class="p-3.5 flex items-center justify-between hover:bg-stone-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-stone-100 text-stone-600 flex items-center justify-center font-mono font-semibold text-xs">1</div>
                            <div>
                                <p class="font-semibold text-stone-900">Aditia Putra</p>
                                <p class="text-[11px] font-mono text-stone-400">NIM: 220403 &bull; Tunggakan: 1 Pekan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-semibold text-rose-800">Rp 10.000</span>
                            <button type="button" class="text-[11px] px-2 py-1 rounded bg-stone-100 hover:bg-emerald-50 hover:text-emerald-800 text-stone-600 transition font-medium">
                                Ingatkan WA
                            </button>
                        </div>
                    </div>

                    <!-- Student 2 -->
                    <div class="p-3.5 flex items-center justify-between hover:bg-stone-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-stone-100 text-stone-600 flex items-center justify-center font-mono font-semibold text-xs">2</div>
                            <div>
                                <p class="font-semibold text-stone-900">Cindy Claudia</p>
                                <p class="text-[11px] font-mono text-stone-400">NIM: 220410 &bull; Tunggakan: 1 Pekan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-semibold text-rose-800">Rp 10.000</span>
                            <button type="button" class="text-[11px] px-2 py-1 rounded bg-stone-100 hover:bg-emerald-50 hover:text-emerald-800 text-stone-600 transition font-medium">
                                Ingatkan WA
                            </button>
                        </div>
                    </div>

                    <!-- Student 3 -->
                    <div class="p-3.5 flex items-center justify-between hover:bg-stone-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-stone-100 text-stone-600 flex items-center justify-center font-mono font-semibold text-xs">3</div>
                            <div>
                                <p class="font-semibold text-stone-900">Kevin Pratama</p>
                                <p class="text-[11px] font-mono text-stone-400">NIM: 220418 &bull; Tunggakan: 1 Pekan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-semibold text-rose-800">Rp 10.000</span>
                            <button type="button" class="text-[11px] px-2 py-1 rounded bg-stone-100 hover:bg-emerald-50 hover:text-emerald-800 text-stone-600 transition font-medium">
                                Ingatkan WA
                            </button>
                        </div>
                    </div>

                    <!-- Student 4 -->
                    <div class="p-3.5 flex items-center justify-between hover:bg-stone-50/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-stone-100 text-stone-600 flex items-center justify-center font-mono font-semibold text-xs">4</div>
                            <div>
                                <p class="font-semibold text-stone-900">Rizky Maulana</p>
                                <p class="text-[11px] font-mono text-stone-400">NIM: 220430 &bull; Tunggakan: 1 Pekan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-semibold text-rose-800">Rp 10.000</span>
                            <button type="button" class="text-[11px] px-2 py-1 rounded bg-stone-100 hover:bg-emerald-50 hover:text-emerald-800 text-stone-600 transition font-medium">
                                Ingatkan WA
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Focus 5: Recent Transactions (Catatan Transaksi Kas Terkini) -->
            <div id="transaksi-kas" class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden"
                 x-data="{ filter: 'semua' }">
                <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-stone-900 text-base tracking-tight">Catatan Transaksi Terbaru</h3>
                        <p class="text-xs text-stone-500">Mutasi kas masuk dan kas keluar kelas TI-3A</p>
                    </div>
                    
                    <!-- Filter Pills -->
                    <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-lg border border-stone-200/80 text-xs">
                        <button type="button" 
                                @click="filter = 'semua'" 
                                :class="filter === 'semua' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                class="px-2.5 py-1 rounded-md transition text-xs">
                            Semua
                        </button>
                        <button type="button" 
                                @click="filter = 'masuk'" 
                                :class="filter === 'masuk' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                class="px-2.5 py-1 rounded-md transition text-xs">
                            Pemasukan
                        </button>
                        <button type="button" 
                                @click="filter = 'keluar'" 
                                :class="filter === 'keluar' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                class="px-2.5 py-1 rounded-md transition text-xs">
                            Pengeluaran
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/80 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Keterangan</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4 text-right">Nominal</th>
                                <th class="py-3 px-4 text-center">Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <!-- Row 1 -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="filter === 'semua' || filter === 'masuk'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">15 Sep 2026</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Setoran Iuran Pekan 9 (Gelombang 2)</div>
                                    <div class="text-[11px] text-stone-400">Setoran 6 mahasiswa &bull; Rekening BCA</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/60">Iuran Kas</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-800 whitespace-nowrap">+Rp 60.000</td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Setoran Gelombang 2', amount: 'Rp 60.000', method: 'Transfer BCA', time: '15 Sep 2026', weeks: 'Pekan 9'})"
                                            class="text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 px-2 py-1 rounded">
                                        Lihat
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="filter === 'semua' || filter === 'keluar'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">12 Sep 2026</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Pembelian Spidol & Penghapus Whiteboard</div>
                                    <div class="text-[11px] text-stone-400">PJ: Bagas (Seksi Perlengkapan)</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 text-stone-700 border border-stone-200">Perlengkapan</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-rose-800 whitespace-nowrap">-Rp 45.000</td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Nota Toko Karunia', amount: 'Rp 45.000', method: 'Cash', time: '12 Sep 2026', weeks: 'Perlengkapan Lab'})"
                                            class="text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 px-2 py-1 rounded">
                                        Nota
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="filter === 'semua' || filter === 'masuk'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">10 Sep 2026</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Setoran Iuran Pekan 9 (Gelombang 1)</div>
                                    <div class="text-[11px] text-stone-400">Setoran 22 mahasiswa lunas</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/60">Iuran Kas</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-800 whitespace-nowrap">+Rp 220.000</td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Mutasi BCA Kas', amount: 'Rp 220.000', method: 'Mutasi Bank', time: '10 Sep 2026', weeks: 'Pekan 9'})"
                                            class="text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 px-2 py-1 rounded">
                                        Lihat
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 4 -->
                            <tr class="hover:bg-stone-50/50 transition" x-show="filter === 'semua' || filter === 'keluar'">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">05 Sep 2026</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">Dana Sosial: Menjenguk Rekan Kelas Sakit (Rian)</div>
                                    <div class="text-[11px] text-stone-400">Parsel buah dan santunan RSUD</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200/60">Dana Sosial</span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-rose-800 whitespace-nowrap">-Rp 150.000</td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <button type="button" 
                                            @click="openProof({name: 'Kuitansi Toko Buah', amount: 'Rp 150.000', method: 'Cash', time: '05 Sep 2026', weeks: 'Dana Sosial'})"
                                            class="text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 px-2 py-1 rounded">
                                        Nota
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3 bg-stone-50 border-t border-stone-100 text-center">
                    <a href="#transaksi-kas" class="text-xs font-semibold text-stone-600 hover:text-stone-900">
                        Buka Seluruh Buku Kas Transaksi &rarr;
                    </a>
                </div>
            </div>

        </div>

        <!-- Right Column: Focus 3. Pending Payments Verification Queue & Focus 6. Quick Actions (lg:col-span-5) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Focus 3: Pending Payments Verification Queue (Verifikasi Pembayaran) -->
            <div id="verifikasi-pembayaran" class="bg-white rounded-xl border border-stone-200/90 shadow-2xs p-5">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-stone-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <h3 class="font-semibold text-stone-900 text-base tracking-tight">Antrean Verifikasi Pembayaran</h3>
                    </div>
                    <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200/60">
                        3 Menunggu
                    </span>
                </div>

                <div class="space-y-3">
                    
                    <!-- Item 1 -->
                    <div class="p-3.5 rounded-lg border border-stone-200 bg-stone-50/60 space-y-2.5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-stone-900 text-xs">Farhan Pratama</h4>
                                <p class="text-[11px] font-mono text-stone-500">NIM: 220412 &bull; QRIS</p>
                            </div>
                            <span class="text-xs font-bold font-mono text-stone-900">Rp 20.000</span>
                        </div>
                        <div class="text-[11px] text-stone-600 flex items-center justify-between">
                            <span>Untuk: <strong>Pekan 9 & 10 (2 Pekan)</strong></span>
                            <span class="text-stone-400">10 menit lalu</span>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="button" 
                                    @click="openProof({name: 'Farhan Pratama', amount: 'Rp 20.000', method: 'Transfer QRIS', time: '16 Sep 2026, 17:50', weeks: 'Pekan 9 dan Pekan 10'})" 
                                    class="flex-1 py-1 px-2 text-[11px] font-medium rounded bg-stone-200/80 hover:bg-stone-300 text-stone-800 transition text-center">
                                Cek Bukti
                            </button>
                            <button type="button" class="py-1 px-3 text-[11px] font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white transition">
                                Setujui
                            </button>
                            <button type="button" class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-600 transition">
                                Tolak
                            </button>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="p-3.5 rounded-lg border border-stone-200 bg-stone-50/60 space-y-2.5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-stone-900 text-xs">Siti Nurhaliza</h4>
                                <p class="text-[11px] font-mono text-stone-500">NIM: 220425 &bull; Transfer BCA</p>
                            </div>
                            <span class="text-xs font-bold font-mono text-stone-900">Rp 10.000</span>
                        </div>
                        <div class="text-[11px] text-stone-600 flex items-center justify-between">
                            <span>Untuk: <strong>Pekan 9</strong></span>
                            <span class="text-stone-400">35 menit lalu</span>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="button" 
                                    @click="openProof({name: 'Siti Nurhaliza', amount: 'Rp 10.000', method: 'Transfer BCA Mobile', time: '16 Sep 2026, 17:25', weeks: 'Pekan 9'})" 
                                    class="flex-1 py-1 px-2 text-[11px] font-medium rounded bg-stone-200/80 hover:bg-stone-300 text-stone-800 transition text-center">
                                Cek Bukti
                            </button>
                            <button type="button" class="py-1 px-3 text-[11px] font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white transition">
                                Setujui
                            </button>
                            <button type="button" class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-600 transition">
                                Tolak
                            </button>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="p-3.5 rounded-lg border border-stone-200 bg-stone-50/60 space-y-2.5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-stone-900 text-xs">Dimas Arya</h4>
                                <p class="text-[11px] font-mono text-stone-500">NIM: 220408 &bull; Mandiri</p>
                            </div>
                            <span class="text-xs font-bold font-mono text-stone-900">Rp 30.000</span>
                        </div>
                        <div class="text-[11px] text-stone-600 flex items-center justify-between">
                            <span>Untuk: <strong>Pekan 9, 10, 11 (3 Pekan)</strong></span>
                            <span class="text-stone-400">1 jam lalu</span>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <button type="button" 
                                    @click="openProof({name: 'Dimas Arya', amount: 'Rp 30.000', method: 'Transfer Mandiri Livin', time: '16 Sep 2026, 16:40', weeks: 'Pekan 9, 10, 11'})" 
                                    class="flex-1 py-1 px-2 text-[11px] font-medium rounded bg-stone-200/80 hover:bg-stone-300 text-stone-800 transition text-center">
                                Cek Bukti
                            </button>
                            <button type="button" class="py-1 px-3 text-[11px] font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white transition">
                                Setujui
                            </button>
                            <button type="button" class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-600 transition">
                                Tolak
                            </button>
                        </div>
                    </div>

                </div>

                <div class="mt-4 pt-3 border-t border-stone-100 text-center">
                    <span class="text-[11px] text-stone-400">Hanya setoran yang disetujui yang menambah saldo kas kelas.</span>
                </div>
            </div>

            <!-- Focus 6: Quick Management Panel -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <h4 class="font-semibold text-stone-900 text-sm mb-3">Tindakan Cepat Bendahara</h4>
                <div class="space-y-2 text-xs">
                    <button type="button" class="w-full flex items-center justify-between p-2.5 rounded-lg bg-stone-50 hover:bg-stone-100 border border-stone-200 transition text-left">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium text-stone-800">Ekspor Laporan Kas Bulanan (PDF)</span>
                        </div>
                        <span class="text-stone-400">&rarr;</span>
                    </button>
                    <button type="button" class="w-full flex items-center justify-between p-2.5 rounded-lg bg-stone-50 hover:bg-stone-100 border border-stone-200 transition text-left">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="font-medium text-stone-800">Rekap Status Pelunasan 32 Siswa</span>
                        </div>
                        <span class="text-stone-400">&rarr;</span>
                    </button>
                    <button type="button" class="w-full flex items-center justify-between p-2.5 rounded-lg bg-stone-50 hover:bg-stone-100 border border-stone-200 transition text-left">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium text-stone-800">Atur Iuran & Rekening Kas</span>
                        </div>
                        <span class="text-stone-400">&rarr;</span>
                    </button>
                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
