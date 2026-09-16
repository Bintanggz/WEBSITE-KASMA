<x-layouts.app role="mahasiswa" title="Dashboard Mahasiswa">

    <!-- Header Greeting & Student Context -->
    <div class="mb-6 pb-4 border-b border-stone-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold text-stone-900 tracking-tight">Halo, Hafizh Al-Fatih</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                    NIM: <span class="font-mono text-stone-700 font-semibold">220401</span> &bull; Kelas: <span class="text-stone-700 font-medium">TI-3A (Teknik Informatika)</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/80 text-xs font-medium">
                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Iuran Pekan Ini Lunas</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Top Focus Section: Current Week Status & Outstanding Payments -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-8">
        
        <!-- Focus 1, 2, & 3: Current Weekly Payment Status, Amount, and Action (md:col-span-7) -->
        <div class="md:col-span-7 bg-white p-5 sm:p-6 rounded-xl border border-stone-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Pekan Berjalan</span>
                        <h3 class="text-lg font-bold text-stone-900 tracking-tight mt-0.5">Pekan ke-9 (Semester Genap)</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                        Status: Lunas
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-stone-50 p-3.5 rounded-lg border border-stone-100">
                        <span class="text-xs text-stone-500 block">Nominal Iuran Pekan Ini</span>
                        <span class="text-2xl font-bold font-mono text-stone-900 mt-1 block">Rp 10.000</span>
                        <span class="text-[11px] text-emerald-700 font-medium mt-0.5 block">&bull; Sudah terverifikasi</span>
                    </div>
                    <div class="bg-stone-50 p-3.5 rounded-lg border border-stone-100">
                        <span class="text-xs text-stone-500 block">Jatuh Tempo Pekan Ini</span>
                        <span class="text-sm font-semibold text-stone-800 mt-2 block">18 Sep 2026</span>
                        <span class="text-[11px] text-stone-400 block">Setiap Jumat perkuliahan</span>
                    </div>
                </div>

                <p class="text-xs text-stone-500 mt-4 leading-relaxed">
                    Pembayaran iuran kas Anda untuk Pekan ke-9 telah diverifikasi oleh bendahara pada 15 September 2026. Anda tidak memiliki tunggakan untuk pekan ini.
                </p>
            </div>

            <!-- Focus 3: Payment Action CTA -->
            <div class="mt-5 pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-center gap-3">
                <button type="button" 
                        @click="paymentModalOpen = true" 
                        class="w-full sm:w-auto flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-800 hover:bg-emerald-900 text-white font-semibold text-xs transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Bayar Kas / Bayar di Muka</span>
                </button>
                <div class="text-[11px] text-stone-500 text-center sm:text-left">
                    <span>Rekening Kas: <strong class="text-stone-700 font-mono">BCA 873-019-2819</strong></span>
                </div>
            </div>
        </div>

        <!-- Focus 4: Outstanding Payments / Tunggakan & Progress (md:col-span-5) -->
        <div class="md:col-span-5 bg-white p-5 sm:p-6 rounded-xl border border-stone-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Status Kewajiban Kas</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <div>
                        <h4 class="text-2xl font-bold font-mono text-stone-900">Rp 0</h4>
                        <span class="text-xs font-semibold text-emerald-800">Tidak ada tunggakan</span>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-stone-100 text-stone-700">
                        Tertib
                    </span>
                </div>

                <!-- Dues Progress Across Semester -->
                <div class="mt-6 pt-4 border-t border-stone-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-stone-600 font-medium">Progres Iuran Semester Ini</span>
                        <span class="font-mono font-semibold text-stone-900">9 / 16 Pekan (56%)</span>
                    </div>
                    <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-700 rounded-full" style="width: 56.25%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-stone-400">
                        <span>Total disetor: <strong class="text-stone-700 font-mono">Rp 90.000</strong></span>
                        <span>Sisa: <strong class="text-stone-700 font-mono">7 Pekan</strong></span>
                    </div>
                </div>
            </div>

            <!-- Mini Matrix Status Strip -->
            <div class="mt-5 pt-4 border-t border-stone-100">
                <span class="text-[11px] text-stone-500 font-medium block mb-2">Pelunasan Pekan 1 s/d 16:</span>
                <div class="grid grid-cols-8 gap-1 text-center font-mono text-[10px]">
                    @for($i = 1; $i <= 8; $i++)
                    <div class="py-1 rounded bg-emerald-100 text-emerald-800 font-semibold" title="Pekan {{ $i }}: Lunas">
                        P{{ $i }}
                    </div>
                    @endfor
                    <div class="py-1 rounded bg-emerald-100 text-emerald-800 font-semibold ring-1 ring-emerald-600" title="Pekan 9: Lunas (Pekan Aktif)">
                        P9
                    </div>
                    @for($i = 10; $i <= 16; $i++)
                    <div class="py-1 rounded bg-stone-100 text-stone-400" title="Pekan {{ $i }}: Mendatang">
                        P{{ $i }}
                    </div>
                    @endfor
                </div>
            </div>
        </div>

    </div>

    <!-- Lower Section: Recent Payment History (Focus 5) & Simple Class Financial Summary (Focus 6) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Focus 5: Recent Personal Payment History (lg:col-span-8) -->
        <div id="riwayat-pembayaran" class="lg:col-span-8 bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-stone-900 text-base tracking-tight">Riwayat Pembayaran Kas Saya</h3>
                    <p class="text-xs text-stone-500">Catatan setoran iuran yang telah diverifikasi oleh bendahara</p>
                </div>
                <span class="text-xs font-mono text-stone-500 bg-stone-100 px-2 py-1 rounded">
                    Total: 9 Transaksi
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50/80 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Keterangan Iuran</th>
                            <th class="py-3 px-4">Metode Bayar</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <tr class="hover:bg-stone-50/50 transition">
                            <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">15 Sep 2026</td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-stone-900 block">Iuran Kas Pekan ke-9</span>
                                <span class="text-[11px] text-stone-400">Semester Genap 2025/2026</span>
                            </td>
                            <td class="py-3.5 px-4 text-stone-700 whitespace-nowrap">Transfer BCA</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">Rp 10.000</td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    Disetujui
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="openProof({name: 'Hafizh Al-Fatih (Pekan 9)', amount: 'Rp 10.000', method: 'Transfer BCA Mobile', time: '15 Sep 2026, 09:12', weeks: 'Pekan ke-9'})"
                                        class="text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 px-2 py-1 rounded transition">
                                    Lihat
                                </button>
                            </td>
                        </tr>

                        <tr class="hover:bg-stone-50/50 transition">
                            <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">08 Sep 2026</td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-stone-900 block">Iuran Kas Pekan ke-8</span>
                                <span class="text-[11px] text-stone-400">Semester Genap 2025/2026</span>
                            </td>
                            <td class="py-3.5 px-4 text-stone-700 whitespace-nowrap">QRIS Kas Kelas</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">Rp 10.000</td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    Disetujui
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="openProof({name: 'Hafizh Al-Fatih (Pekan 8)', amount: 'Rp 10.000', method: 'QRIS Kasma', time: '08 Sep 2026, 11:30', weeks: 'Pekan ke-8'})"
                                        class="text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 px-2 py-1 rounded transition">
                                    Lihat
                                </button>
                            </td>
                        </tr>

                        <tr class="hover:bg-stone-50/50 transition">
                            <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">01 Sep 2026</td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-stone-900 block">Iuran Kas Pekan ke-7</span>
                                <span class="text-[11px] text-stone-400">Semester Genap 2025/2026</span>
                            </td>
                            <td class="py-3.5 px-4 text-stone-700 whitespace-nowrap">Transfer BCA</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">Rp 10.000</td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    Disetujui
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <button type="button" 
                                        @click="openProof({name: 'Hafizh Al-Fatih (Pekan 7)', amount: 'Rp 10.000', method: 'Transfer BCA Mobile', time: '01 Sep 2026, 08:45', weeks: 'Pekan ke-7'})"
                                        class="text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 px-2 py-1 rounded transition">
                                    Lihat
                                </button>
                            </td>
                        </tr>

                        <tr class="hover:bg-stone-50/50 transition">
                            <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">25 Agu 2026</td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-stone-900 block">Iuran Kas Pekan ke-6</span>
                                <span class="text-[11px] text-stone-400">Semester Genap 2025/2026</span>
                            </td>
                            <td class="py-3.5 px-4 text-stone-700 whitespace-nowrap">Tunai (Di Kelas)</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">Rp 10.000</td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                    Disetujui
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center text-stone-400 whitespace-nowrap">
                                &mdash;
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-stone-50 border-t border-stone-100 text-center">
                <span class="text-xs text-stone-500">Menampilkan 4 setoran terakhir Anda &bull; Semua tercatat dalam kasir kelas</span>
            </div>
        </div>

        <!-- Focus 6: Simple Class Financial Summary (lg:col-span-4) -->
        <div id="keuangan-kelas" class="lg:col-span-4 space-y-5">
            
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
                    <div>
                        <h4 class="font-semibold text-stone-900 text-sm">Transparansi Kas Kelas</h4>
                        <p class="text-xs text-stone-500">Ringkasan kas kelas TI-3A</p>
                    </div>
                    <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded bg-stone-100 text-stone-600">Terbuka</span>
                </div>

                <!-- Simple Saldo Box -->
                <div class="bg-stone-50 p-3.5 rounded-lg border border-stone-100 space-y-1 mb-4">
                    <span class="text-[11px] text-stone-500 block">Total Sisa Saldo Kas Kelas</span>
                    <span class="text-2xl font-bold font-mono text-stone-900 block">Rp 3.700.000</span>
                    <span class="text-[11px] text-stone-400 block font-mono">BCA Kas: 873-019-2819</span>
                </div>

                <!-- In & Out summary -->
                <div class="grid grid-cols-2 gap-2 text-xs mb-4">
                    <div class="p-2.5 rounded-lg border border-stone-100 bg-stone-50/50">
                        <span class="text-stone-400 block text-[10px] uppercase">Pemasukan</span>
                        <span class="font-bold font-mono text-emerald-800 text-xs">Rp 4.850.000</span>
                    </div>
                    <div class="p-2.5 rounded-lg border border-stone-100 bg-stone-50/50">
                        <span class="text-stone-400 block text-[10px] uppercase">Pengeluaran</span>
                        <span class="font-bold font-mono text-stone-800 text-xs">Rp 1.150.000</span>
                    </div>
                </div>

                <!-- Recent Spending Snippet -->
                <div>
                    <span class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider block mb-2">Pengeluaran Terakhir Kelas:</span>
                    <ul class="space-y-2 text-xs">
                        <li class="flex items-center justify-between pb-1.5 border-b border-stone-50">
                            <span class="text-stone-700 truncate pr-2">Spidol & Penghapus Lab</span>
                            <span class="font-mono text-stone-900 font-medium shrink-0">-Rp 45.000</span>
                        </li>
                        <li class="flex items-center justify-between pb-1.5 border-b border-stone-50">
                            <span class="text-stone-700 truncate pr-2">Dana Sosial Rekan Sakit</span>
                            <span class="font-mono text-stone-900 font-medium shrink-0">-Rp 150.000</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="text-stone-700 truncate pr-2">Fotokopi Modul Basis Data</span>
                            <span class="font-mono text-stone-900 font-medium shrink-0">-Rp 280.000</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bendahara Contact Box -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/80 text-xs">
                <p class="font-semibold text-stone-900 mb-1">Ada pertanyaan soal kas?</p>
                <p class="text-stone-500 text-[11px] mb-3">Hubungi bendahara kelas untuk konfirmasi bukti atau dispensasi.</p>
                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-stone-200">
                    <div>
                        <p class="font-medium text-stone-800 text-xs">Nadya Putri (Bendahara)</p>
                        <p class="text-[10px] text-stone-500 font-mono">0821-9876-5432</p>
                    </div>
                    <span class="text-[10px] text-emerald-800 font-semibold bg-emerald-50 px-2 py-0.5 rounded">WhatsApp</span>
                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
