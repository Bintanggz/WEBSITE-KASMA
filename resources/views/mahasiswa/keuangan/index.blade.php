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
        <div class="mb-6 pb-4 border-b border-zinc-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Transparansi Keuangan Kas Kelas</h2>
                    <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                        Rekapitulasi riil seluruh arus kas masuk dan belanja operasional kelas TI26A3 secara terbuka dan akuntabel.
                    </p>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-zinc-100 text-zinc-700 text-xs font-medium border border-zinc-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    <span>Buku Kas Terbuka &amp; Terverifikasi</span>
                </div>
            </div>
        </div>

        <!-- 3 Summary Metrics Clean Strip -->
        <div class="bg-white rounded-xl border border-zinc-200 p-5 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 divide-y sm:divide-y-0 sm:divide-x divide-zinc-100">
                <!-- Saldo Kas Riil -->
                <div class="pt-3 sm:pt-0 sm:px-4 first:sm:pl-0">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Saldo Kas Terkini</span>
                    <div class="text-2xl font-bold {{ $currentBalance >= 0 ? 'text-zinc-900' : 'text-rose-600' }} tracking-tight mt-1 font-mono">
                        Rp {{ number_format($currentBalance, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1 mt-1 text-[11px] text-zinc-400">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentBalance >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        <span>Total Bersih Kas (Pemasukan &minus; Pengeluaran)</span>
                    </div>
                </div>

                <!-- Total Pemasukan -->
                <div class="pt-3 sm:pt-0 sm:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Pemasukan</span>
                    <div class="text-2xl font-bold text-emerald-700 tracking-tight mt-1 font-mono">
                        +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1 mt-1 text-[11px] text-zinc-400">
                        <span>Akumulasi iuran mahasiswa &amp; kas masuk</span>
                    </div>
                </div>

                <!-- Total Pengeluaran -->
                <div class="pt-3 sm:pt-0 sm:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Pengeluaran</span>
                    <div class="text-2xl font-bold text-zinc-900 tracking-tight mt-1 font-mono">
                        -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1 mt-1 text-[11px] text-zinc-400">
                        <span>Total belanja kegiatan &amp; operasional kelas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs & Search -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                <a href="{{ route('mahasiswa.keuangan.index', ['type' => 'all', 'search' => request('search')]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $type === 'all' ? 'bg-zinc-900 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
                    Semua Transaksi
                </a>
                <a href="{{ route('mahasiswa.keuangan.index', ['type' => 'income', 'search' => request('search')]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $type === 'income' ? 'bg-emerald-700 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
                    Pemasukan Saja
                </a>
                <a href="{{ route('mahasiswa.keuangan.index', ['type' => 'expense', 'search' => request('search')]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $type === 'expense' ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
                    Pengeluaran Saja
                </a>
            </div>

            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('mahasiswa.keuangan.index') }}" class="flex items-center gap-1.5">
                    @if(request('type') && request('type') !== 'all')
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    <div class="relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari transaksi..."
                               class="w-44 sm:w-56 pl-8 pr-7 py-1.5 text-xs rounded-lg border border-zinc-200 bg-white placeholder-zinc-400 focus:outline-none focus:border-zinc-400">
                        <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        @if(request('search'))
                            <a href="{{ route('mahasiswa.keuangan.index', ['type' => request('type', 'all')]) }}"
                               class="absolute right-2 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600"
                               title="Reset pencarian">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>
                    <button type="submit" class="px-2.5 py-1.5 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-xs font-medium rounded-lg border border-zinc-200 transition cursor-pointer">
                        Cari
                    </button>
                </form>

                <div class="hidden md:block text-xs text-zinc-400 whitespace-nowrap">
                    <span class="font-medium text-zinc-700">{{ $transactions->total() }}</span> data
                </div>
            </div>
        </div>

        <!-- Ledger Table Card -->
        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
            <!-- Desktop Table View -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-zinc-50 text-zinc-400 border-b border-zinc-200 uppercase font-semibold text-[10px] tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Arus &amp; Kategori</th>
                            <th class="py-3 px-4">Deskripsi / Keterangan</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Bukti Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-zinc-50/70 transition">
                                <td class="py-3.5 px-4 font-mono text-zinc-600 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($tx->type === 'income')
                                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                            <span class="font-medium text-emerald-800">Kas Masuk</span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-zinc-400"></span>
                                            <span class="font-medium text-zinc-700">Pengeluaran</span>
                                        @endif
                                        <span class="text-[10px] text-zinc-400">&bull; {{ ucfirst(str_replace('_', ' ', $tx->category)) }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-zinc-800">
                                    <span class="font-medium">{{ $tx->description }}</span>
                                    @if($tx->payment && $tx->payment->studentDue && $tx->payment->studentDue->user)
                                        <span class="block text-[11px] text-zinc-400">
                                            Dari: {{ $tx->payment->studentDue->user->name }} &bull; {{ $tx->payment->studentDue->cashPeriod->name ?? 'Kas' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold whitespace-nowrap {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-zinc-900' }}">
                                    {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $hasReceipt = $tx->receipt_path || ($tx->payment && $tx->payment->proof_file_path);
                                    @endphp
                                    @if($hasReceipt)
                                        <button type="button"
                                                @click="openReceipt('{{ route('transactions.receipt', $tx) }}', '{{ addslashes($tx->description) }}')"
                                                class="px-2 py-0.5 rounded text-[11px] font-medium text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                                            Lihat Nota
                                        </button>
                                    @else
                                        <span class="text-zinc-300 font-mono">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-zinc-400">
                                    Tidak ada catatan transaksi yang sesuai dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="sm:hidden divide-y divide-zinc-100">
                @forelse($transactions as $tx)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-mono text-zinc-500">
                                {{ \Carbon\Carbon::parse($tx->transaction_date)->translatedFormat('d M Y') }}
                            </span>
                            <span class="font-mono font-bold {{ $tx->type === 'income' ? 'text-emerald-700' : 'text-zinc-900' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-zinc-900 text-xs">{{ $tx->description }}</p>
                            @if($tx->payment && $tx->payment->studentDue && $tx->payment->studentDue->user)
                                <p class="text-[10px] text-zinc-400">
                                    Dari: {{ $tx->payment->studentDue->user->name }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center justify-between pt-1 text-[11px]">
                            <span class="text-zinc-500">
                                {{ $tx->type === 'income' ? 'Kas Masuk' : 'Pengeluaran' }} &bull; {{ ucfirst(str_replace('_', ' ', $tx->category)) }}
                            </span>
                            @if($tx->receipt_path || ($tx->payment && $tx->payment->proof_file_path))
                                <button type="button"
                                        @click="openReceipt('{{ route('transactions.receipt', $tx) }}', '{{ addslashes($tx->description) }}')"
                                        class="text-zinc-600 hover:text-zinc-900 font-medium underline cursor-pointer">
                                    Nota &rarr;
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-zinc-400 text-xs">
                        Tidak ada catatan transaksi yang sesuai dengan filter.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="p-3.5 bg-zinc-50 border-t border-zinc-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        <!-- Receipt Modal Dialog -->
        <div x-show="receiptModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="receiptModalOpen = false">
            <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto"
                 @click.away="receiptModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="font-semibold text-zinc-900 text-sm" x-text="'Bukti Nota: ' + receiptTitle"></h3>
                    <button type="button" @click="receiptModalOpen = false" class="text-zinc-400 hover:text-zinc-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4 space-y-3">
                    <div class="w-full max-h-96 overflow-hidden rounded-lg border border-zinc-200 bg-zinc-100 flex items-center justify-center p-1">
                        <img :src="receiptUrl" :alt="receiptTitle" class="w-full max-h-96 object-contain rounded" />
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <a :href="receiptUrl" target="_blank" rel="noopener noreferrer" class="text-xs text-zinc-600 hover:text-zinc-900 font-medium underline">
                            Buka di tab baru &rarr;
                        </a>
                        <button type="button" @click="receiptModalOpen = false" class="py-1.5 px-3 text-xs font-medium rounded-lg text-zinc-700 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
