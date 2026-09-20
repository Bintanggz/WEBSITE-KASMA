<x-layouts.app role="bendahara" title="Buku Transaksi Kas Kelas">

    <div x-data="{
        addModalOpen: false,
        addType: 'income',
        editModalOpen: false,
        editData: { id: null, type: 'income', amount: '', category: '', description: '', transaction_date: '' },
        deleteModalOpen: false,
        deleteData: { id: null, description: '', amount: '' },
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
        }
    }">

        <!-- Header Banner -->
        <div class="mb-6 pb-4 border-b border-stone-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-stone-900 tracking-tight">Buku Transaksi Kas &amp; Pembukuan Kelas</h2>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Rekapitulasi lengkap pemasukan dan pengeluaran kas kelas berbasis saldo buku besar riil.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="openAdd('income')"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Pemasukan Manual</span>
                    </button>

                    <button type="button" 
                            @click="openAdd('expense')"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-rose-800 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                        <span>Catat Pengeluaran</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 3 Primary Balance & Ledger Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <!-- Saldo Kas Riil -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Saldo Kas Terkini (Ledger)</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                    Rp {{ number_format($currentBalance, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">
                    Total Pemasukan dikurangi Total Pengeluaran
                </span>
            </div>

            <!-- Total Pemasukan -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Pemasukan Sah</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-emerald-800 mt-2 block">
                    + Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">
                    {{ $incomeCount }} transaksi kas masuk
                </span>
            </div>

            <!-- Total Pengeluaran -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Pengeluaran Kas</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-rose-700 mt-2 block">
                    - Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">
                    {{ $expenseCount }} transaksi pengeluaran tercatat
                </span>
            </div>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs mb-6 p-4">
            <form method="GET" action="{{ route('bendahara.transaksi.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
                <!-- Tipe Tab -->
                <div class="sm:col-span-3">
                    <label class="block text-stone-600 font-medium mb-1">Tipe Transaksi</label>
                    <select name="type" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua Tipe Transaksi</option>
                        <option value="income" {{ $type === 'income' ? 'selected' : '' }}>Hanya Pemasukan (+)</option>
                        <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Hanya Pengeluaran (-)</option>
                    </select>
                </div>

                <!-- Dari Tanggal -->
                <div class="sm:col-span-3">
                    <label class="block text-stone-600 font-medium mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                </div>

                <!-- Sampai Tanggal -->
                <div class="sm:col-span-3">
                    <label class="block text-stone-600 font-medium mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                </div>

                <!-- Cari Kata Kunci -->
                <div class="sm:col-span-3 flex items-end gap-1.5">
                    <div class="flex-1">
                        <label class="block text-stone-600 font-medium mb-1">Cari Keterangan / Kategori</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari..." class="w-full bg-stone-50 border border-stone-200 rounded-lg px-2.5 py-1.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                    </div>
                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition cursor-pointer">
                        Filter
                    </button>
                    @if($type !== 'all' || $startDate || $endDate || $search)
                        <a href="{{ route('bendahara.transaksi.index') }}" class="px-2.5 py-1.5 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition" title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-stone-900 text-base tracking-tight">Rincian Buku Kas</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Catatan seluruh transaksi masuk dan keluar</p>
                </div>
                <span class="text-xs font-mono text-stone-600 bg-stone-100 px-2.5 py-1 rounded-md">
                    Total: {{ $transactions->total() }} Catatan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50/90 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Keterangan Transaksi</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4 text-center">Asal Catatan</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Bukti / Nota</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($transactions as $t)
                            @php
                                $isIncome = $t->type === 'income';
                                $isPayment = $t->isPaymentBased();
                                $hasReceipt = $t->receipt_path || ($t->payment && $t->payment->proof_file_path);
                                $receiptUrl = $hasReceipt ? route('transactions.receipt', $t) : null;
                            @endphp
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-3.5 px-4 font-mono text-stone-600 whitespace-nowrap">
                                    {{ $t->transaction_date ? $t->transaction_date->format('d M Y') : $t->created_at->format('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4 max-w-sm">
                                    <div class="font-medium text-stone-900">{{ $t->description }}</div>
                                    <div class="text-[10px] text-stone-400">
                                        Dicatat oleh: {{ $t->creator->name ?? 'Bendahara' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 text-stone-700 border border-stone-200">
                                        {{ $t->category }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($isPayment)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60" title="Dibuat otomatis dari verifikasi setoran mahasiswa">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            <span>Iuran Siswa</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                            Manual
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold whitespace-nowrap {{ $isIncome ? 'text-emerald-800' : 'text-rose-700' }}">
                                    {{ $isIncome ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($hasReceipt)
                                        <button type="button" 
                                                @click="openProof({
                                                    name: '{{ addslashes($t->description) }}',
                                                    amount: '{{ ($isIncome ? '+ ' : '- ') . number_format($t->amount, 0, ',', '.') }}',
                                                    method: '{{ $t->category }}',
                                                    time: '{{ $t->transaction_date ? $t->transaction_date->format('d M Y') : '-' }}',
                                                    weeks: '{{ $isIncome ? 'Pemasukan Kas' : 'Pengeluaran Kas' }}',
                                                    proof_url: '{{ $receiptUrl }}',
                                                    payment_id: null,
                                                    is_pending: false
                                                })"
                                                class="px-2 py-1 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition cursor-pointer">
                                            Lihat Berkas
                                        </button>
                                    @else
                                        <span class="text-stone-300 text-[11px]">&mdash;</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    @if($t->isManual())
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openEdit({
                                                        id: {{ $t->id }},
                                                        type: '{{ $t->type }}',
                                                        amount: '{{ (int) $t->amount }}',
                                                        category: '{{ addslashes($t->category) }}',
                                                        description: '{{ addslashes($t->description) }}',
                                                        transaction_date: '{{ $t->transaction_date ? $t->transaction_date->format('Y-m-d') : '' }}'
                                                    })"
                                                    class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition cursor-pointer">
                                                Edit
                                            </button>
                                            <button type="button" 
                                                    @click="openDelete({{ $t->id }}, '{{ addslashes($t->description) }}', 'Rp {{ number_format($t->amount, 0, ',', '.') }}')"
                                                    class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-rose-50 hover:text-rose-700 text-stone-600 transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-stone-400 italic">Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400">
                                    <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    <p class="font-medium text-stone-600 text-sm">Tidak Ada Transaksi Ditemukan</p>
                                    <p class="text-xs text-stone-400 mt-1">Coba sesuaikan filter pencarian atau tanggal transaksi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3.5 bg-stone-50 border-t border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-stone-500">
                <span>Saldo kas dihitung dari selisih seluruh entri buku besar yang sah</span>
                @if($transactions->hasPages())
                    <div>{{ $transactions->links() }}</div>
                @endif
            </div>
        </div>

        <!-- MODAL: Tambah Transaksi Manual (Income / Expense) -->
        <div x-show="addModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-xs"
             @keydown.escape.window="addModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="addModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full" :class="addType === 'income' ? 'bg-emerald-600' : 'bg-rose-600'"></span>
                        <h3 class="font-semibold text-stone-900 text-sm" x-text="addType === 'income' ? 'Tambah Pemasukan Manual' : 'Catat Pengeluaran Kas'"></h3>
                    </div>
                    <button type="button" @click="addModalOpen = false" class="text-stone-400 hover:text-stone-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('bendahara.transaksi.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <input type="hidden" name="type" :value="addType">

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Tipe Transaksi</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" 
                                    @click="addType = 'income'"
                                    :class="addType === 'income' ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-semibold' : 'bg-stone-50 border-stone-200 text-stone-600'"
                                    class="py-2 px-3 rounded-lg border text-center transition cursor-pointer">
                                Pemasukan (+)
                            </button>
                            <button type="button" 
                                    @click="addType = 'expense'"
                                    :class="addType === 'expense' ? 'bg-rose-50 border-rose-300 text-rose-900 font-semibold' : 'bg-stone-50 border-stone-200 text-stone-600'"
                                    class="py-2 px-3 rounded-lg border text-center transition cursor-pointer">
                                Pengeluaran (-)
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Nominal (Rp) <span class="text-rose-600 font-bold">*</span></label>
                        <input type="number" name="amount" min="1000" step="500" placeholder="Contoh: 50000" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400 font-mono" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Kategori <span class="text-rose-600 font-bold">*</span></label>
                            <input type="text" name="category" placeholder="Contoh: Perlengkapan, Sosial, Donasi" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                        </div>
                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Tanggal Transaksi <span class="text-rose-600 font-bold">*</span></label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Keterangan / Deskripsi <span class="text-rose-600 font-bold">*</span></label>
                        <textarea name="description" rows="2" placeholder="Jelaskan peruntukan transaksi secara ringkas..." class="w-full bg-stone-50 border border-stone-200 rounded-lg p-2.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required></textarea>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Unggah Berkas Nota / Kuitansi (Opsional)</label>
                        <input type="file" name="receipt_file" accept="image/*" class="w-full text-xs text-stone-600 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-stone-200 file:text-stone-800 hover:file:bg-stone-300">
                        <span class="text-[10px] text-stone-400 mt-0.5 block">Format JPG, PNG, atau WEBP &bull; Maks 5MB</span>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                            Simpan Transaksi
                        </button>
                        <button type="button" @click="addModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Edit Transaksi Manual -->
        <div x-show="editModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-xs"
             @keydown.escape.window="editModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="editModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        <h3 class="font-semibold text-stone-900 text-sm">Edit Transaksi Manual</h3>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="text-stone-400 hover:text-stone-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/bendahara/transaksi/' + editData.id" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Tipe Transaksi</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" 
                                    @click="editData.type = 'income'"
                                    :class="editData.type === 'income' ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-semibold' : 'bg-stone-50 border-stone-200 text-stone-600'"
                                    class="py-2 px-3 rounded-lg border text-center transition cursor-pointer">
                                Pemasukan (+)
                            </button>
                            <button type="button" 
                                    @click="editData.type = 'expense'"
                                    :class="editData.type === 'expense' ? 'bg-rose-50 border-rose-300 text-rose-900 font-semibold' : 'bg-stone-50 border-stone-200 text-stone-600'"
                                    class="py-2 px-3 rounded-lg border text-center transition cursor-pointer">
                                Pengeluaran (-)
                            </button>
                        </div>
                        <input type="hidden" name="type" :value="editData.type">
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Nominal (Rp) <span class="text-rose-600 font-bold">*</span></label>
                        <input type="number" name="amount" min="1000" step="500" x-model="editData.amount" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400 font-mono" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Kategori <span class="text-rose-600 font-bold">*</span></label>
                            <input type="text" name="category" x-model="editData.category" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                        </div>
                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Tanggal Transaksi <span class="text-rose-600 font-bold">*</span></label>
                            <input type="date" name="transaction_date" x-model="editData.transaction_date" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Keterangan / Deskripsi <span class="text-rose-600 font-bold">*</span></label>
                        <textarea name="description" rows="2" x-model="editData.description" class="w-full bg-stone-50 border border-stone-200 rounded-lg p-2.5 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required></textarea>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Ganti Berkas Bukti / Nota (Opsional)</label>
                        <input type="file" name="receipt_file" accept="image/*" class="w-full text-xs text-stone-600 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-stone-200 file:text-stone-800 hover:file:bg-stone-300">
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                            Perbarui Transaksi
                        </button>
                        <button type="button" @click="editModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Konfirmasi Hapus Transaksi Manual -->
        <div x-show="deleteModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-xs"
             @keydown.escape.window="deleteModalOpen = false">
            <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-xl border border-stone-200" 
                 @click.away="deleteModalOpen = false">
                <div class="flex items-center space-x-2 pb-3 border-b border-stone-100">
                    <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                    <h3 class="font-semibold text-stone-900 text-sm">Hapus Transaksi Manual</h3>
                </div>

                <div class="mt-4 space-y-2 text-xs text-stone-600">
                    <p>Apakah Anda yakin ingin menghapus catatan transaksi ini dari pembukuan kas kelas?</p>
                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-lg">
                        <p class="font-semibold text-stone-900" x-text="deleteData.description"></p>
                        <p class="font-mono text-stone-700 mt-0.5" x-text="deleteData.amount"></p>
                    </div>
                    <p class="text-[11px] text-stone-400">Saldo kas akan otomatis disesuaikan setelah transaksi dihapus.</p>
                </div>

                <form :action="'/bendahara/transaksi/' + deleteData.id" method="POST" class="mt-5 flex gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-rose-700 hover:bg-rose-800 transition shadow-2xs cursor-pointer">
                        Hapus Transaksi
                    </button>
                    <button type="button" @click="deleteModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                        Batal
                    </button>
                </form>
            </div>
        </div>

    </div>

</x-layouts.app>
