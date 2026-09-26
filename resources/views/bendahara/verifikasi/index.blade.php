<x-layouts.app role="bendahara" title="Verifikasi Pembayaran Mahasiswa">

    <div x-data="{
        rejectModalOpen: false,
        rejectData: { id: null, name: '' },
        cashModalOpen: false,
        selectedDueId: '',
        openReject(id, name) {
            this.rejectData = { id: id, name: name };
            this.rejectModalOpen = true;
        }
    }">

        <!-- Header -->
        <div class="mb-6 pb-4 border-b border-zinc-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Verifikasi Pembayaran Kas Mahasiswa</h2>
                        @if($pendingCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $pendingCount }} Menunggu Tinjauan
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                        Tinjau bukti transfer digital mahasiswa, setujui untuk menambah saldo kas kelas, atau catat setoran tunai.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="cashModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Catat Setoran Tunai</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Clean Summary Metrics Strip -->
        <div class="bg-white rounded-xl border border-zinc-200 p-5 mb-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 divide-y lg:divide-y-0 lg:divide-x divide-zinc-100">
                <div class="pt-3 lg:pt-0 lg:px-4 first:lg:pl-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Menunggu Verifikasi</span>
                        <span class="w-2 h-2 rounded-full {{ $pendingCount > 0 ? 'bg-amber-500' : 'bg-zinc-300' }}"></span>
                    </div>
                    <span class="text-2xl font-bold font-mono text-amber-900 mt-1 block">
                        {{ $pendingCount }} Setoran
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Bukti transfer belum disetujui</span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Disetujui</span>
                    <span class="text-2xl font-bold font-mono text-emerald-800 mt-1 block">
                        {{ $approvedCount }} Transaksi
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Sah tercatat dalam buku kas</span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Pembayaran Ditolak</span>
                    <span class="text-2xl font-bold font-mono {{ $rejectedCount > 0 ? 'text-rose-600' : 'text-zinc-900' }} mt-1 block">
                        {{ $rejectedCount }} Transaksi
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Bukti tidak sesuai / tidak valid</span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Kas Masuk Disetujui</span>
                    <span class="text-2xl font-bold font-mono text-zinc-900 mt-1 block">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block font-mono">Akumulasi iuran terverifikasi</span>
                </div>
            </div>
        </div>

        <!-- Filter Tabs & Table -->
        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
            <div class="p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-zinc-900 text-sm tracking-tight">Daftar Pengajuan Setoran Mahasiswa</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Tinjau dan periksa berkas bukti transfer untuk validasi pembukuan</p>
                </div>

                <!-- Status Filter Links -->
                <div class="flex items-center gap-1 bg-zinc-100 p-1 rounded-lg text-xs">
                    <a href="{{ route('bendahara.verifikasi.index', ['status' => 'pending']) }}"
                       class="px-2.5 py-1 rounded-md transition text-xs {{ $status === 'pending' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800' }}">
                        Menunggu ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('bendahara.verifikasi.index', ['status' => 'approved']) }}"
                       class="px-2.5 py-1 rounded-md transition text-xs {{ $status === 'approved' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800' }}">
                        Disetujui ({{ $approvedCount }})
                    </a>
                    <a href="{{ route('bendahara.verifikasi.index', ['status' => 'rejected']) }}"
                       class="px-2.5 py-1 rounded-md transition text-xs {{ $status === 'rejected' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800' }}">
                        Ditolak ({{ $rejectedCount }})
                    </a>
                    <a href="{{ route('bendahara.verifikasi.index', ['status' => 'all']) }}"
                       class="px-2.5 py-1 rounded-md transition text-xs {{ $status === 'all' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800' }}">
                        Semua ({{ $totalCount }})
                    </a>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold border-b border-zinc-100">
                        <tr>
                            <th class="py-3 px-4">Mahasiswa</th>
                            <th class="py-3 px-4">Pekan Kas</th>
                            <th class="py-3 px-4">Waktu Setor</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($payments as $p)
                            @php
                                $student = $p->studentDue?->user;
                                $periodName = $p->studentDue?->cashPeriod?->name ?? 'Pekan Kas';
                                $dueId = $p->student_due_id;
                                $proofUrl = $p->proof_file_path ? route('payments.proof', $p) : null;
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-zinc-900">{{ $student->name ?? 'Mahasiswa' }}</div>
                                    <div class="text-[10px] font-mono text-zinc-400">NIM: {{ $student->nim ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-medium text-zinc-800">{{ $periodName }}</span>
                                    <span class="text-[10px] text-zinc-400 block">{{ $p->studentDue?->cashPeriod?->academic_year ?? '' }}</span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-zinc-600 font-mono">
                                    {{ $p->payment_date ? $p->payment_date->format('d M Y, H:i') : $p->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($p->payment_method === 'cash')
                                        <span class="text-zinc-700">Tunai</span>
                                    @elseif($p->payment_method === 'qris')
                                        <span class="text-zinc-700">QRIS Kas</span>
                                    @else
                                        <span class="text-zinc-700">Transfer BCA</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-zinc-900 whitespace-nowrap">
                                    Rp {{ number_format($p->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($p->isApproved())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @elseif($p->isPending())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($p->proof_file_path)
                                            <button type="button" 
                                                    @click="openProof({
                                                        name: '{{ addslashes($student->name ?? 'Mahasiswa') }} ({{ $periodName }})',
                                                        amount: 'Rp {{ number_format($p->amount, 0, ',', '.') }}',
                                                        method: '{{ $p->payment_method === 'qris' ? 'QRIS Kas' : 'Transfer BCA' }}',
                                                        time: '{{ $p->payment_date ? $p->payment_date->format('d M Y, H:i') : $p->created_at->format('d M Y, H:i') }}',
                                                        weeks: '{{ $periodName }}',
                                                        proof_url: '{{ $proofUrl }}',
                                                        payment_id: {{ $p->id }},
                                                        is_pending: {{ $p->isPending() ? 'true' : 'false' }}
                                                    })" 
                                                    class="py-1 px-2.5 text-[11px] font-medium rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-700 transition cursor-pointer">
                                                Bukti
                                            </button>
                                        @endif

                                        @if($p->isPending())
                                            <form action="{{ route('bendahara.payments.approve', $p) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="py-1 px-2.5 text-[11px] font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white transition cursor-pointer">
                                                    Setujui
                                                </button>
                                            </form>

                                            <button type="button" 
                                                    @click="openReject({{ $p->id }}, '{{ addslashes($student->name ?? 'Mahasiswa') }} ({{ $periodName }})')"
                                                    class="py-1 px-2 text-[11px] font-medium rounded bg-zinc-100 hover:bg-rose-50 hover:text-rose-700 text-zinc-600 transition cursor-pointer">
                                                Tolak
                                            </button>
                                        @elseif($p->isRejected())
                                            <span class="text-[10px] text-zinc-400 italic" title="{{ $p->rejection_reason }}">
                                                {{ Str::limit($p->rejection_reason, 20) }}
                                            </span>
                                        @else
                                            <span class="text-[11px] text-emerald-700 font-medium">Sah Terverifikasi</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-zinc-400">
                                    <svg class="w-10 h-10 text-zinc-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="font-medium text-zinc-600 text-xs">Tidak Ada Data Setoran</p>
                                    <p class="text-[11px] text-zinc-400 mt-0.5">Tidak ada transaksi pembayaran pada kategori filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination / Footer -->
            <div class="p-3.5 bg-zinc-50 border-t border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-zinc-500">
                <span>Hanya pembayaran yang disetujui yang menambah saldo pembukuan kas kelas</span>
                @if($payments->hasPages())
                    <div>{{ $payments->links() }}</div>
                @endif
            </div>
        </div>

        <!-- MODAL: Tolak Pembayaran -->
        <div x-show="rejectModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="rejectModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200" 
                 @click.away="rejectModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm" x-text="'Tolak Pembayaran - ' + rejectData.name"></h3>
                    </div>
                    <button type="button" @click="rejectModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/bendahara/payments/' + rejectData.id + '/reject'" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Alasan Penolakan</label>
                        <textarea name="rejection_reason" rows="3" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg p-3 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>Bukti transfer buram/tidak terbaca atau nominal tidak sesuai.</textarea>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-rose-700 hover:bg-rose-800 transition cursor-pointer">
                            Konfirmasi Tolak
                        </button>
                        <button type="button" @click="rejectModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Catat Setoran Tunai -->
        <div x-show="cashModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="cashModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="cashModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm">Catat Setoran Tunai Langsung</h3>
                    </div>
                    <button type="button" @click="cashModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @php
                    $unpaidDuesForCash = \App\Models\StudentDue::where('status', 'unpaid')
                        ->with(['user', 'cashPeriod'])
                        ->get()
                        ->filter(fn($d) => $d->user && $d->cashPeriod);
                @endphp

                @if($unpaidDuesForCash->count() > 0)
                <form method="POST" action="{{ route('bendahara.cash-payments.store') }}" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Pilih Mahasiswa &amp; Pekan Iuran</label>
                        <select name="student_due_id" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                            @foreach($unpaidDuesForCash as $due)
                                <option value="{{ $due->id }}">
                                    {{ $due->user->name }} &bull; {{ $due->cashPeriod->name }} (Rp {{ number_format($due->amount, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-700">
                        <p class="font-semibold text-[11px] uppercase tracking-wider text-zinc-500">Pemberitahuan</p>
                        <p class="text-[11px] text-zinc-600 pt-0.5">
                            Setoran tunai otomatis berstatus disetujui lunas dan langsung menambahkan saldo kas buku besar.
                        </p>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 transition cursor-pointer">
                            Simpan Pembayaran Tunai
                        </button>
                        <button type="button" @click="cashModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
                @else
                <div class="p-6 text-center text-zinc-500 text-xs">
                    Tidak ada tunggakan kas mahasiswa yang belum dibayar saat ini.
                </div>
                @endif
            </div>
        </div>

    </div>

</x-layouts.app>
