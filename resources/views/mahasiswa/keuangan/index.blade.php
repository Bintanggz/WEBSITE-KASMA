<x-layouts.app role="mahasiswa" title="Transparansi Keuangan Kas Kelas">

    <div x-data="{
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-xl font-bold text-stone-900 tracking-tight">Transparansi Keuangan Kas Kelas</h2>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Rekapitulasi riil seluruh arus kas masuk dan belanja pengeluaran kas kelas secara terbuka dan akuntabel.
                    </p>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-stone-100 text-stone-700 text-xs font-medium border border-stone-200">
                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Buku Kas Terbuka &amp; Terverifikasi</span>
                </div>
            </div>
        </div>

        <!-- 3 Summary Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <!-- Saldo Kas Riil -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Saldo Kas Terkini</span>
                <div class="text-2xl font-bold {{ $currentBalance >= 0 ? 'text-stone-900' : 'text-rose-600' }} tracking-tight mt-1.5">
                    Rp {{ number_format($currentBalance, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1 mt-2 text-[11px] text-stone-500">
                    <span class="w-1.5 h-1.5 rounded-full {{ $currentBalance >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    <span>Total Bersih Kas (Pemasukan &minus; Pengeluaran)</span>
                </div>
            </div>

            <!-- Total Pemasukan -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider block">Total Pemasukan</span>
                <div class="text-2xl font-bold text-emerald-700 tracking-tight mt-1.5">
                    +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1 mt-2 text-[11px] text-stone-500">
                    <span>Akumulasi iuran mahasiswa &amp; pemasukan manual</span>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-rose-800 uppercase tracking-wider block">Total Pengeluaran</span>
                <div class="text-2xl font-bold text-rose-600 tracking-tight mt-1.5">
                    -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </div>
                <div class="flex items-center gap-1 mt-2 text-[11px] text-stone-500">
                    <span>Total belanja kegiatan &amp; operasional kelas</span>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                <a href="{{ route('mahasiswa.keuangan.index', ['type' => 'all']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $type === 'all' ? 'bg-stone-900 text-white shadow-2xs' : 'bg-white text-stone-600 border border-stone-200 hover:bg-stone-50' }}">
                    Semua Transaksi
                </a>
                <a href="{{ route('mahasiswa.keuangan.index', ['type' => 'income']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $type === 'income' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-white text-stone-600 border border-stone-200 hover:bg-stone-50' }}">
                    Pemasukan Saja
                </a>
                <a href="{{ route('mahasiswa.keuangan.index', ['type' => 'expense']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $type === 'expense' ? 'bg-rose-600 text-white shadow-2xs' : 'bg-white text-stone-600 border border-stone-200 hover:bg-stone-50' }}">
                    Pengeluaran Saja
                </a>
            </div>

            <div class="text-xs text-stone-500">
                Menampilkan <span class="font-medium text-stone-800">{{ $transactions->total() }}</span> catatan transaksi
            </div>
        </div>

        <!-- Ledger Table Card -->
        <div class="bg-white rounded-xl border border-stone-200 shadow-2xs overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-500 border-b border-stone-200 uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Arus &amp; Kategori</th>
                            <th class="py-3 px-4">Deskripsi / Keterangan</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Bukti Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-stone-50/70 transition">
                                <td class="py-3.5 px-4 font-mono text-stone-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        @if($tx->type === 'income')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Pemasukan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/70">
                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4" />
                                                </svg>
                                                Pengeluaran
                                            </span>
                                        @endif

                                        <span class="text-stone-700 font-medium">
                                            {{ $tx->category ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-stone-800 font-medium max-w-xs truncate" title="{{ $tx->description }}">
                                    {{ $tx->description }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-rose-600' }}">
                                    {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($tx->receipt_path || $tx->payment?->proof_file_path)
                                        <button type="button"
                                                @click="openReceipt('{{ route('transactions.receipt', $tx) }}', '{{ addslashes($tx->description) }}')"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded text-[11px] font-medium text-stone-700 bg-stone-100 hover:bg-stone-200 border border-stone-200/80 transition cursor-pointer">
                                            <svg class="w-3 h-3 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat Bukti</span>
                                        </button>
                                    @else
                                        <span class="text-stone-400 text-[11px] italic">Tanpa Nota</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-stone-400">
                                    <svg class="w-9 h-9 mx-auto mb-2 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-xs font-medium text-stone-500">Belum ada catatan transaksi pada filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-stone-100">
                @forelse($transactions as $tx)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-[11px] text-stone-500">
                                {{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }}
                            </span>
                            @if($tx->type === 'income')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                    + Pemasukan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/70">
                                    &minus; Pengeluaran
                                </span>
                            @endif
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-stone-900">
                                {{ $tx->description }}
                            </div>
                            <div class="text-[11px] text-stone-500 mt-0.5">
                                Kategori: <span class="text-stone-700 font-medium">{{ $tx->category ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-stone-50">
                            <div class="font-mono font-bold text-sm {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </div>

                            @if($tx->receipt_path || $tx->payment?->proof_file_path)
                                <button type="button"
                                        @click="openReceipt('{{ route('transactions.receipt', $tx) }}', '{{ addslashes($tx->description) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-medium text-stone-700 bg-stone-100 hover:bg-stone-200 transition">
                                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Bukti Nota</span>
                                </button>
                            @else
                                <span class="text-stone-400 text-xs italic">Tanpa Nota</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-stone-400">
                        <p class="text-xs font-medium text-stone-500">Belum ada catatan transaksi pada filter ini.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="p-4 border-t border-stone-200 bg-stone-50">
                    {{ $transactions->links() }}
                </div>
            @endif
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
                    <button type="button" @click="receiptModalOpen = false" class="text-stone-400 hover:text-stone-600 p-1">
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
                    <button type="button" @click="receiptModalOpen = false" class="px-3.5 py-1.5 text-xs font-medium rounded-lg text-stone-700 bg-stone-100 hover:bg-stone-200 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
