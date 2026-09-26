<x-layouts.app role="bendahara" title="Buku Transaksi Kas Kelas">

    <div x-data="{
        addModalOpen: false,
        addType: 'income',
        editModalOpen: false,
        editData: { id: null, type: 'income', amount: '', category: '', description: '', transaction_date: '' },
        deleteModalOpen: false,
        deleteData: { id: null, description: '', amount: '' },
        receiptModalOpen: false,
        receiptUrl: '',
        receiptTitle: '',
        openAdd(type) {
            this.addType = type;
            this.addModalOpen = true;
        },
        openEdit(item) {
            this.editData = { ...item };
            this.editModalOpen = true;
        },
        openDelete(id, description, amount) {
            this.deleteData = { id: id, description: description, amount: amount };
            this.deleteModalOpen = true;
        },
        openReceipt(url, title) {
            this.receiptUrl = url;
            this.receiptTitle = title;
            this.receiptModalOpen = true;
        }
    }">

        <!-- Header Banner -->
        <div class="mb-6 pb-4 border-b border-zinc-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Buku Transaksi Kas &amp; Pembukuan Kelas</h2>
                    <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                        Rekapitulasi lengkap pemasukan dan pengeluaran kas kelas berbasis saldo buku besar riil.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="openAdd('income')"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Pemasukan Manual</span>
                    </button>

                    <button type="button" 
                            @click="openAdd('expense')"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                        <span>Catat Pengeluaran</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3 Primary Balance & Ledger Metric Strip -->
        <div class="bg-white rounded-xl border border-zinc-200 p-5 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 divide-y sm:divide-y-0 sm:divide-x divide-zinc-100">
                <!-- Saldo Kas Riil -->
                <div class="pt-3 sm:pt-0 sm:px-4 first:sm:pl-0">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Saldo Kas Terkini (Ledger)</span>
                    <span class="text-2xl font-bold font-mono text-zinc-900 mt-1 block">
                        Rp {{ number_format($currentBalance, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">
                        Total Pemasukan dikurangi Total Pengeluaran
                    </span>
                </div>

                <!-- Total Pemasukan -->
                <div class="pt-3 sm:pt-0 sm:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Pemasukan Sah</span>
                    <span class="text-2xl font-bold font-mono text-emerald-800 mt-1 block">
                        + Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">
                        {{ $incomeCount }} transaksi kas masuk
                    </span>
                </div>

                <!-- Total Pengeluaran -->
                <div class="pt-3 sm:pt-0 sm:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Pengeluaran Kas</span>
                    <span class="text-2xl font-bold font-mono text-zinc-900 mt-1 block">
                        - Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">
                        {{ $expenseCount }} transaksi pengeluaran tercatat
                    </span>
                </div>
            </div>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="bg-white rounded-xl border border-zinc-200 mb-6 p-4">
            <form method="GET" action="{{ route('bendahara.transaksi.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
                <!-- Tipe Tab -->
                <div class="sm:col-span-3">
                    <label class="block text-zinc-600 font-medium mb-1">Tipe Transaksi</label>
                    <select name="type" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-800 focus:outline-none focus:border-zinc-400">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Tipe Transaksi</option>
                        <option value="income" {{ $type === 'income' ? 'selected' : '' }}>Hanya Pemasukan (+)</option>
                        <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Hanya Pengeluaran (-)</option>
                    </select>
                </div>

                <!-- Dari Tanggal -->
                <div class="sm:col-span-3">
                    <label class="block text-zinc-600 font-medium mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-800 focus:outline-none focus:border-zinc-400">
                </div>

                <!-- Sampai Tanggal -->
                <div class="sm:col-span-3">
                    <label class="block text-zinc-600 font-medium mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-800 focus:outline-none focus:border-zinc-400">
                </div>

                <!-- Cari Kata Kunci -->
                <div class="sm:col-span-3 flex items-end gap-1.5">
                    <div class="flex-1">
                        <label class="block text-zinc-600 font-medium mb-1">Cari Keterangan</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari..." class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-2.5 py-1.5 text-xs text-zinc-800 focus:outline-none focus:border-zinc-400">
                    </div>
                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                        Filter
                    </button>
                    @if($type !== 'all' || $startDate || $endDate || $search)
                        <a href="{{ route('bendahara.transaksi.index') }}" class="px-2.5 py-1.5 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition" title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
            <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-zinc-900 text-sm tracking-tight">Rincian Buku Kas</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Catatan seluruh transaksi masuk dan keluar</p>
                </div>
                <div class="text-xs text-zinc-400">
                    Total: <span class="font-semibold text-zinc-800">{{ $transactions->total() }}</span> data transaksi
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold border-b border-zinc-100">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Tipe &amp; Kategori</th>
                            <th class="py-3 px-4">Deskripsi / Keterangan</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Bukti Nota</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($transactions as $trx)
                            @php
                                $isIncome = $trx->type === 'income';
                                $receiptUrl = ($trx->receipt_path || $trx->payment?->proof_file_path) ? route('transactions.receipt', $trx) : null;
                                $formattedDate = $trx->transaction_date ? \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') : $trx->created_at->format('d M Y');
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="py-3.5 px-4 font-mono text-zinc-600 whitespace-nowrap">
                                    {{ $formattedDate }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        @if($isIncome)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                Pemasukan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-700 border border-zinc-200">
                                                Pengeluaran
                                            </span>
                                        @endif
                                        <span class="text-[11px] text-zinc-400">{{ ucfirst(str_replace('_', ' ', $trx->category)) }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-zinc-900">
                                    <div class="font-medium">{{ $trx->description }}</div>
                                    @if($trx->payment && $trx->payment->studentDue && $trx->payment->studentDue->user)
                                        <div class="text-[11px] text-zinc-400">
                                            Mahasiswa: {{ $trx->payment->studentDue->user->name }} &bull; {{ $trx->payment->studentDue->cashPeriod->name ?? 'Kas' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold whitespace-nowrap {{ $isIncome ? 'text-emerald-700' : 'text-zinc-900' }}">
                                    {{ $isIncome ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($receiptUrl)
                                        <button type="button" 
                                                @click="openReceipt('{{ $receiptUrl }}', '{{ addslashes($trx->description) }}')"
                                                class="text-[11px] text-zinc-600 hover:text-zinc-900 bg-zinc-100 hover:bg-zinc-200 px-2 py-0.5 rounded transition cursor-pointer">
                                            Lihat Nota
                                        </button>
                                    @else
                                        <span class="text-zinc-300 font-mono">&mdash;</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$trx->payment_id)
                                            <!-- Editable manual transaction -->
                                            <button type="button" 
                                                    @click="openEdit({
                                                        id: {{ $trx->id }},
                                                        type: '{{ $trx->type }}',
                                                        amount: {{ $trx->amount }},
                                                        category: '{{ $trx->category }}',
                                                        description: '{{ addslashes($trx->description) }}',
                                                        transaction_date: '{{ $trx->transaction_date ? \Carbon\Carbon::parse($trx->transaction_date)->format('Y-m-d') : $trx->created_at->format('Y-m-d') }}'
                                                    })"
                                                    class="p-1 text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 rounded transition cursor-pointer"
                                                    title="Ubah Transaksi">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <button type="button" 
                                                    @click="openDelete({{ $trx->id }}, '{{ addslashes($trx->description) }}', 'Rp {{ number_format($trx->amount, 0, ',', '.') }}')"
                                                    class="p-1 text-zinc-400 hover:text-rose-600 hover:bg-rose-50 rounded transition cursor-pointer"
                                                    title="Hapus Transaksi">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @else
                                            <span class="text-[10px] text-zinc-400 italic">Iuran Terverifikasi</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-zinc-400">
                                    <svg class="w-10 h-10 text-zinc-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium text-zinc-600 text-xs">Belum Ada Transaksi</p>
                                    <p class="text-[11px] text-zinc-400 mt-0.5">Tidak ditemukan data mutasi kas sesuai kriteria pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="p-3.5 bg-zinc-50 border-t border-zinc-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: Tambah Transaksi Manual (Income or Expense) -->
        <div x-show="addModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="addModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="addModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full" :class="addType === 'income' ? 'bg-emerald-600' : 'bg-zinc-800'"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm" x-text="addType === 'income' ? 'Catat Pemasukan Kas Manual' : 'Catat Pengeluaran Kas Kelas'"></h3>
                    </div>
                    <button type="button" @click="addModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('bendahara.transaksi.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <input type="hidden" name="type" :value="addType">
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" min="1000" step="500" placeholder="contoh: 50000" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Kategori</label>
                        <template x-if="addType === 'income'">
                            <select name="category" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                                <option value="Iuran Kas">Iuran Kas</option>
                                <option value="Sumbangan">Sumbangan / Donasi</option>
                                <option value="Dana Usaha">Hasil Usaha / Jualan Kelas</option>
                                <option value="Lainnya">Penerimaan Lainnya</option>
                            </select>
                        </template>
                        <template x-if="addType === 'expense'">
                            <select name="category" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                                <option value="Perlengkapan">Perlengkapan Lab / Kelas</option>
                                <option value="Dana Sosial">Dana Sosial & Rekan Sakit</option>
                                <option value="Akademik">Akademik & Modul Kuliah</option>
                                <option value="Kegiatan">Kegiatan & Acara Kelas</option>
                                <option value="Lainnya">Lain-lain</option>
                            </select>
                        </template>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Keterangan / Deskripsi</label>
                        <input type="text" name="description" placeholder="contoh: Pembelian spidol & penghapus" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Foto Bukti / Nota (Opsional)</label>
                        <input type="file" name="receipt_file" accept="image/*" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs text-zinc-700">
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" 
                                class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white transition cursor-pointer"
                                :class="addType === 'income' ? 'bg-emerald-700 hover:bg-emerald-800' : 'bg-zinc-900 hover:bg-zinc-800'">
                            Simpan Transaksi
                        </button>
                        <button type="button" @click="addModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Edit Transaksi Manual -->
        <div x-show="editModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="editModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="editModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="font-semibold text-zinc-900 text-sm">Ubah Catatan Transaksi</h3>
                    <button type="button" @click="editModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/bendahara/transaksi/' + editData.id" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Nominal (Rp)</label>
                        <input type="number" name="amount" min="1000" step="500" x-model="editData.amount" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Kategori</label>
                        <input type="text" name="category" x-model="editData.category" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" x-model="editData.transaction_date" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Keterangan / Deskripsi</label>
                        <input type="text" name="description" x-model="editData.description" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Ganti Foto Bukti / Nota (Opsional)</label>
                        <input type="file" name="receipt_file" accept="image/*" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs text-zinc-700">
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                            Perbarui Transaksi
                        </button>
                        <button type="button" @click="editModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Hapus Transaksi -->
        <div x-show="deleteModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="deleteModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200" 
                 @click.away="deleteModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm">Hapus Transaksi Kas</h3>
                    </div>
                    <button type="button" @click="deleteModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3 text-xs">
                    <p class="text-zinc-600">
                        Apakah Anda yakin ingin menghapus catatan transaksi ini? Saldo kas buku besar akan disesuaikan otomatis.
                    </p>
                    <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg">
                        <p class="font-semibold text-zinc-900" x-text="deleteData.description"></p>
                        <p class="text-zinc-500 font-mono mt-0.5" x-text="deleteData.amount"></p>
                    </div>

                    <form :action="'/bendahara/transaksi/' + deleteData.id" method="POST" class="pt-2">
                        @csrf
                        @method('DELETE')
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-rose-700 hover:bg-rose-800 transition cursor-pointer">
                                Ya, Hapus Transaksi
                            </button>
                            <button type="button" @click="deleteModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Preview Bukti Nota -->
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
