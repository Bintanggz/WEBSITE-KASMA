<x-layouts.app role="bendahara" title="Laporan & Rekapitulasi Kas Kelas">

    <div x-data="{
        activeTab: '{{ request()->has('type') || request()->has('start_date') || request()->has('cash_period_id') ? 'transaksi' : 'mingguan' }}',
        receiptModalOpen: false,
        receiptUrl: '',
        receiptTitle: '',
        openReceipt(url, title) {
            this.receiptUrl = url;
            this.receiptTitle = title;
            this.receiptModalOpen = true;
        }
    }">

        <!-- Header -->
        <div class="mb-6 pb-4 border-b border-stone-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-stone-900 tracking-tight">Laporan &amp; Rekapitulasi Kas Kelas</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-stone-100 text-stone-700 border border-stone-200">
                            Buku Besar Riil
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Rekapitulasi keuangan kas kelas TI26A3, kepatuhan iuran mingguan mahasiswa, dan mutasi pembukuan.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('bendahara.laporan.print') }}" 
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Laporan Resmi</span>
                    </a>

                    <a href="{{ route('bendahara.transaksi.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-stone-700 bg-white border border-stone-200 hover:bg-stone-50 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Kelola Buku Transaksi</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3 Primary Balance & Ledger Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <!-- Saldo Kas Riil -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Saldo Kas Terkini (Ledger)</span>
                <div class="text-2xl font-bold {{ $currentBalance >= 0 ? 'text-stone-900' : 'text-rose-600' }} tracking-tight mt-1.5 font-mono">
                    Rp {{ number_format($currentBalance, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1 mt-2 text-[11px] text-stone-500">
                    <span class="w-1.5 h-1.5 rounded-full {{ $currentBalance >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    <span>Total Bersih: Pemasukan &minus; Pengeluaran</span>
                </div>
            </div>

            <!-- Total Pemasukan -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider block">Total Pemasukan</span>
                <div class="text-2xl font-bold text-emerald-700 tracking-tight mt-1.5 font-mono">
                    +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1 mt-2 text-[11px] text-stone-500">
                    <span>Iuran mahasiswa &amp; penerimaan manual</span>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-rose-800 uppercase tracking-wider block">Total Pengeluaran</span>
                <div class="text-2xl font-bold text-rose-600 tracking-tight mt-1.5 font-mono">
                    -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1 mt-2 text-[11px] text-stone-500">
                    <span>Total belanja operasional &amp; kegiatan</span>
                </div>
            </div>
        </div>

        <!-- Tab Switcher Navigation -->
        <div class="border-b border-stone-200 mb-6">
            <nav class="flex space-x-2 sm:space-x-4 overflow-x-auto pb-1 text-xs font-semibold">
                <button type="button"
                        @click="activeTab = 'mingguan'"
                        :class="activeTab === 'mingguan' ? 'border-stone-900 text-stone-900 border-b-2 font-bold' : 'text-stone-500 hover:text-stone-700 border-b-2 border-transparent'"
                        class="pb-2.5 px-1.5 whitespace-nowrap transition cursor-pointer">
                    Rekapitulasi Iuran Mingguan ({{ $cashPeriods->count() }} Pekan)
                </button>
                <button type="button"
                        @click="activeTab = 'mahasiswa'"
                        :class="activeTab === 'mahasiswa' ? 'border-stone-900 text-stone-900 border-b-2 font-bold' : 'text-stone-500 hover:text-stone-700 border-b-2 border-transparent'"
                        class="pb-2.5 px-1.5 whitespace-nowrap transition cursor-pointer">
                    Kepatuhan Mahasiswa ({{ $totalActiveStudents }} Orang)
                </button>
                <button type="button"
                        @click="activeTab = 'transaksi'"
                        :class="activeTab === 'transaksi' ? 'border-stone-900 text-stone-900 border-b-2 font-bold' : 'text-stone-500 hover:text-stone-700 border-b-2 border-transparent'"
                        class="pb-2.5 px-1.5 whitespace-nowrap transition cursor-pointer">
                    Riwayat Transaksi &amp; Filter Mutasi
                </button>
            </nav>
        </div>

        <!-- TAB 1: Rekapitulasi Iuran Mingguan -->
        <div x-show="activeTab === 'mingguan'" x-cloak class="space-y-4">
            <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-stone-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-stone-900 text-sm">Progress Pengumpulan Kas per Pekan</h3>
                        <p class="text-xs text-stone-500">Rekapitulasi jumlah mahasiswa lunas dan persentase iuran terkumpul per pekan</p>
                    </div>
                    <span class="text-xs font-mono text-stone-500 bg-stone-100 px-2 py-1 rounded">
                        Target per Siswa: Rp 10.000 / pekan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Pekan</th>
                                <th class="py-3 px-4">Jatuh Tempo</th>
                                <th class="py-3 px-4">Status &amp; Mahasiswa Lunas</th>
                                <th class="py-3 px-4">Progress</th>
                                <th class="py-3 px-4 text-right">Terkumpul / Target</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($weeklySummaries as $item)
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="py-3.5 px-4 font-semibold text-stone-900 whitespace-nowrap">
                                        {{ $item->period->name }}
                                        @if($item->period->is_active)
                                            <span class="ml-1.5 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                        {{ $item->period->due_date ? $item->period->due_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="font-medium text-stone-900">{{ $item->paid_count }}</span>
                                        <span class="text-stone-400">/ {{ $totalActiveStudents }} Mahasiswa</span>
                                        @if($item->unpaid_count > 0)
                                            <span class="ml-1 text-[10px] font-semibold text-rose-700 bg-rose-50 px-1 py-0.5 rounded">
                                                {{ $item->unpaid_count }} belum
                                            </span>
                                        @else
                                            <span class="ml-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1 py-0.5 rounded">
                                                Lunas 100%
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 w-40">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-1.5 bg-stone-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-700 rounded-full" style="width: {{ min($item->percentage, 100) }}%"></div>
                                            </div>
                                            <span class="text-[11px] font-mono font-semibold text-stone-700 w-10 text-right">
                                                {{ $item->percentage }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono whitespace-nowrap">
                                        <span class="font-bold text-stone-900">Rp {{ number_format($item->collected_amount, 0, ',', '.') }}</span>
                                        <span class="text-stone-400 text-[11px]">/ Rp {{ number_format($item->target_amount, 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-stone-400">
                                        Belum ada periode kas yang dibuat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: Kepatuhan Mahasiswa -->
        <div x-show="activeTab === 'mahasiswa'" x-cloak class="space-y-4" x-data="{ stuSearch: '' }">
            <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-stone-900 text-sm">Status Kepatuhan Seluruh Mahasiswa</h3>
                        <p class="text-xs text-stone-500">Daftar rekapitulasi pelunasan kas per individu mahasiswa kelas TI26A3</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Quick Search Input -->
                        <div class="relative">
                            <input type="text"
                                   x-model="stuSearch"
                                   placeholder="Cari nama / NIM..."
                                   class="w-40 sm:w-52 pl-7 pr-3 py-1.5 text-xs rounded-lg border border-stone-200 bg-stone-50/70 placeholder-stone-400 focus:outline-none focus:bg-white focus:border-stone-400">
                            <svg class="w-3.5 h-3.5 text-stone-400 absolute left-2 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <!-- Export CSV -->
                        <a href="{{ route('bendahara.laporan.export.compliance') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-stone-700 bg-white border border-stone-200 hover:bg-stone-50 transition shadow-2xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Ekspor CSV</span>
                        </a>

                        <span class="text-xs font-mono text-stone-500 bg-stone-100 px-2 py-1.5 rounded-lg border border-stone-200">
                            Total: {{ $studentSummaries->count() }}
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Nama Mahasiswa</th>
                                <th class="py-3 px-4">NIM</th>
                                <th class="py-3 px-4 text-center">Pekan Lunas</th>
                                <th class="py-3 px-4 text-center">Pekan Tertunggak</th>
                                <th class="py-3 px-4 text-right">Total Disetor</th>
                                <th class="py-3 px-4 text-right">Sisa Tunggakan</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($studentSummaries as $stu)
                                <tr class="hover:bg-stone-50/50 transition"
                                    x-show="!stuSearch || '{{ strtolower(addslashes($stu->name)) }}'.includes(stuSearch.toLowerCase()) || '{{ $stu->nim ?? '' }}'.includes(stuSearch.toLowerCase())">
                                    <td class="py-3 px-4 font-semibold text-stone-900 whitespace-nowrap">
                                        {{ $stu->name }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-stone-500 whitespace-nowrap">
                                        {{ $stu->nim ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-semibold text-emerald-800 whitespace-nowrap">
                                        {{ $stu->paid_weeks_count }} Pekan
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono whitespace-nowrap {{ $stu->unpaid_weeks_count > 0 ? 'text-rose-700 font-semibold' : 'text-stone-400' }}">
                                        {{ $stu->unpaid_weeks_count }} Pekan
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">
                                        Rp {{ number_format($stu->total_paid_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono whitespace-nowrap {{ $stu->total_unpaid_amount > 0 ? 'text-rose-700 font-bold' : 'text-stone-400' }}">
                                        Rp {{ number_format($stu->total_unpaid_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if($stu->unpaid_weeks_count === 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                                Lunas Penuh
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/70">
                                                Tertunggak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if($stu->unpaid_weeks_count > 0)
                                            @php
                                                $rawPhone = $stu->phone_number ?? '';
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                                }
                                                $nominalOwed = number_format($stu->total_unpaid_amount, 0, ',', '.');
                                                $reminderMsg = "Halo {$stu->name},\n\nMengingatkan dari Bendahara Kelas TI26A3, saat ini terdapat tagihan kas kelas yang belum lunas sebanyak {$stu->unpaid_weeks_count} pekan dengan total nominal Rp {$nominalOwed}.\n\nPembayaran dapat disetorkan melalui Transfer BCA 1234567890 a.n. Kas Kelas TI26A3 atau scan QRIS di website KASMA.\n\nMohon segera konfirmasi atau unggah bukti transfer ya. Terima kasih! 🙏";
                                            @endphp
                                            @if($cleanPhone)
                                                <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($reminderMsg) }}"
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium rounded-md text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 transition cursor-pointer"
                                                   title="Kirim pengingat tagihan via WhatsApp">
                                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/>
                                                    </svg>
                                                    <span>Ingatkan WA</span>
                                                </a>
                                            @else
                                                <span class="text-[10px] text-stone-400 italic">Tanpa No WA</span>
                                            @endif
                                        @else
                                            <span class="text-stone-400 text-[11px]">&minus;</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-stone-400">
                                        Belum ada data mahasiswa terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: Mutasi & Riwayat Transaksi -->
        <div x-show="activeTab === 'transaksi'" x-cloak class="space-y-4">
            <!-- Filter Toolbar Card -->
            <div class="bg-white p-4 rounded-xl border border-stone-200/90 shadow-2xs">
                <form method="GET" action="{{ route('bendahara.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                    <!-- Tipe Transaksi -->
                    <div>
                        <label class="block text-stone-600 font-medium mb-1">Jenis Transaksi</label>
                        <select name="type" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Arus Kas</option>
                            <option value="income" {{ $type === 'income' ? 'selected' : '' }}>Pemasukan Saja</option>
                            <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Pengeluaran Saja</option>
                        </select>
                    </div>

                    <!-- Dari Tanggal -->
                    <div>
                        <label class="block text-stone-600 font-medium mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                    </div>

                    <!-- Sampai Tanggal -->
                    <div>
                        <label class="block text-stone-600 font-medium mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                    </div>

                    <!-- Periode Kas -->
                    <div>
                        <label class="block text-stone-600 font-medium mb-1">Periode Kas</label>
                        <select name="cash_period_id" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                            <option value="all">Semua Pekan</option>
                            @foreach($cashPeriods as $p)
                                <option value="{{ $p->id }}" {{ (string)$cashPeriodId === (string)$p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-1.5 px-3 rounded-lg bg-stone-900 hover:bg-stone-800 text-white font-semibold text-xs transition cursor-pointer">
                            Filter
                        </button>
                        <a href="{{ route('bendahara.laporan.index') }}" class="py-1.5 px-3 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 font-medium text-xs transition text-center cursor-pointer">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-stone-900 text-sm">Buku Catatan Mutasi Kas</h3>
                        <p class="text-xs text-stone-500">Menampilkan {{ $transactions->total() }} catatan transaksi sesuai filter</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('bendahara.laporan.export.transactions', request()->all()) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-stone-700 bg-white border border-stone-200 hover:bg-stone-50 transition shadow-2xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Ekspor CSV</span>
                        </a>
                        <span class="text-xs font-mono text-stone-500 bg-stone-100 px-2.5 py-1.5 rounded-lg border border-stone-200">
                            {{ $transactions->total() }} Data
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Arus &amp; Kategori</th>
                                <th class="py-3 px-4">Keterangan Transaksi</th>
                                <th class="py-3 px-4 text-right">Nominal</th>
                                <th class="py-3 px-4 text-center">Bukti Nota</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="py-3 px-4 font-mono text-stone-600 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            @if($tx->type === 'income')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                                    Pemasukan
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/70">
                                                    Pengeluaran
                                                </span>
                                            @endif
                                            <span class="font-medium text-stone-700">{{ $tx->category ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <p class="font-medium text-stone-900">{{ $tx->description }}</p>
                                        @if($tx->payment?->studentDue)
                                            <p class="text-[10px] text-stone-400 font-mono">
                                                Iuran: {{ $tx->payment->studentDue->user->name ?? 'Mahasiswa' }} &bull; {{ $tx->payment->studentDue->cashPeriod->name ?? '' }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @if($tx->receipt_path || $tx->payment?->proof_file_path)
                                            <button type="button"
                                                    @click="openReceipt('{{ route('transactions.receipt', $tx) }}', '{{ addslashes($tx->description) }}')"
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-medium text-stone-700 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                                                <svg class="w-3 h-3 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Lihat Nota</span>
                                            </button>
                                        @else
                                            <span class="text-stone-400 text-[11px] italic">Tanpa Nota</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-stone-400">
                                        Belum ada catatan mutasi transaksi pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="p-3.5 bg-stone-50 border-t border-stone-200">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Receipt Modal Viewer -->
        <div x-show="receiptModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             @keydown.escape.window="receiptModalOpen = false">
            <div class="bg-white rounded-2xl max-w-xl w-full p-5 sm:p-6 shadow-xl border border-stone-200 relative"
                 @click.outside="receiptModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                    <div>
                        <h4 class="text-sm font-bold text-stone-900">Lampiran Bukti Nota / Kuitansi</h4>
                        <p class="text-xs text-stone-500 truncate max-w-xs sm:max-w-md" x-text="receiptTitle"></p>
                    </div>
                    <button type="button" @click="receiptModalOpen = false" class="text-stone-400 hover:text-stone-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4 flex flex-col items-center justify-center bg-stone-100 rounded-xl p-2 min-h-60 max-h-96 overflow-hidden">
                    <img :src="receiptUrl" alt="Bukti Transaksi" class="max-h-80 max-w-full object-contain rounded-lg shadow-2xs">
                </div>
                <div class="mt-4 flex justify-between items-center">
                    <a :href="receiptUrl" target="_blank" class="text-xs text-stone-600 hover:text-stone-900 font-medium inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        <span>Buka ukuran penuh di tab baru</span>
                    </a>
                    <button type="button" @click="receiptModalOpen = false" class="px-3.5 py-1.5 text-xs font-medium rounded-lg text-stone-700 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
