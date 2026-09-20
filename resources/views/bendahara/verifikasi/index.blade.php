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
        <div class="mb-6 pb-4 border-b border-stone-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-stone-900 tracking-tight">Verifikasi Pembayaran Kas Mahasiswa</h2>
                        @if($pendingCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/70">
                                {{ $pendingCount }} Menunggu Tinjauan
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Tinjau bukti transfer digital mahasiswa, setujui untuk menambah saldo kas kelas, atau catat setoran tunai.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="cashModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Catat Setoran Tunai</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4 Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Menunggu Verifikasi</span>
                    <span class="w-2 h-2 rounded-full {{ $pendingCount > 0 ? 'bg-amber-500' : 'bg-stone-300' }}"></span>
                </div>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-amber-900 mt-2 block">
                    {{ $pendingCount }} Setoran
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Bukti transfer baru belum diverifikasi</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Disetujui</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-emerald-800 mt-2 block">
                    {{ $approvedCount }} Transaksi
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Telah tercatat sah dalam kas kelas</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Pembayaran Ditolak</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono {{ $rejectedCount > 0 ? 'text-rose-700' : 'text-stone-900' }} mt-2 block">
                    {{ $rejectedCount }} Transaksi
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Bukti buram atau tidak sesuai</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Dana Iuran Terkumpul</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block font-mono">Akumulasi seluruh iuran disetujui</span>
            </div>
        </div>

        <!-- Filter Tabs & Table -->
        <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-stone-50/50">
                <div>
                    <h3 class="font-bold text-stone-900 text-base tracking-tight">Daftar Pengajuan Setoran Mahasiswa</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Tinjau dan periksa berkas bukti transfer untuk validasi pembukuan</p>
                </div>

                <!-- Status Filter Links -->
                <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-lg border border-stone-200/80 text-xs">
                    <a href="{{ route('bendahara.verifikasi.index', ['status' => 'pending']) }}"
                       class="px-2.5 py-1 rounded-md transition text-xs {{ $status === 'pending' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800' }}">
                        Menunggu ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('bendahara.verifikasi.index', ['status' => 'approved']) }}"
                       class="px-2.5 py-1 rounded-md transition text-xs {{ $status === 'approved' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800' }}">
                        Disetujui ({{ $approvedCount }})
                    </a>
                    <a href="{{ route('bendahara.verifikasi.index', ['status' => 'rejected']) }}"
                       class="px-2.5 py-1 rounded-md transition text-xs {{ $status === 'rejected' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800' }}">
                        Ditolak ({{ $rejectedCount }})
                    </a>
                    <a href="{{ route('bendahara.verifikasi.index', ['status' => 'all']) }}"
                       class="px-2.5 py-1 rounded-md transition text-xs {{ $status === 'all' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800' }}">
                        Semua ({{ $totalCount }})
                    </a>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50/90 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
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
                    <tbody class="divide-y divide-stone-100">
                        @forelse($payments as $p)
                            @php
                                $student = $p->studentDue?->user;
                                $periodName = $p->studentDue?->cashPeriod?->name ?? 'Pekan Kas';
                                $dueId = $p->student_due_id;
                                $proofUrl = $p->proof_file_path ? route('payments.proof', $p) : null;
                            @endphp
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-stone-900">{{ $student->name ?? 'Mahasiswa' }}</div>
                                    <div class="text-[11px] font-mono text-stone-500">NIM: {{ $student->nim ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-semibold text-stone-800">{{ $periodName }}</span>
                                    <span class="text-[10px] text-stone-400 block">{{ $p->studentDue?->cashPeriod?->academic_year ?? '' }}</span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-stone-700 font-mono">
                                    {{ $p->payment_date ? $p->payment_date->format('d M Y, H:i') : $p->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($p->payment_method === 'cash')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 text-stone-700 border border-stone-200">
                                            Tunai (Langsung)
                                        </span>
                                    @elseif($p->payment_method === 'qris')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-800 border border-blue-200/60">
                                            QRIS Kas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                            Transfer BCA
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">
                                    Rp {{ number_format($p->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($p->isApproved())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                            <svg class="w-3 h-3 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Disetujui</span>
                                        </span>
                                    @elseif($p->isPending())
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/80">
                                            <svg class="w-3 h-3 text-amber-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Menunggu</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/80">
                                            <svg class="w-3 h-3 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span>Ditolak</span>
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
                                                    class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition cursor-pointer">
                                                Cek Bukti
                                            </button>
                                        @endif

                                        @if($p->isPending())
                                            <form action="{{ route('bendahara.payments.approve', $p) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="py-1 px-2.5 text-[11px] font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white transition shadow-2xs cursor-pointer">
                                                    Setujui
                                                </button>
                                            </form>

                                            <button type="button" 
                                                    @click="openReject({{ $p->id }}, '{{ addslashes($student->name ?? 'Mahasiswa') }} ({{ $periodName }})')"
                                                    class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-rose-50 hover:text-rose-700 text-stone-600 transition cursor-pointer">
                                                Tolak
                                            </button>
                                        @elseif($p->isRejected())
                                            <span class="text-[10px] text-stone-400 italic" title="{{ $p->rejection_reason }}">
                                                {{ Str::limit($p->rejection_reason, 20) }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-emerald-700 font-medium">Sah Terverifikasi</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400">
                                    <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="font-medium text-stone-600 text-sm">Tidak Ada Data Setoran</p>
                                    <p class="text-xs text-stone-400 mt-1">Tidak ada transaksi pembayaran pada kategori filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination / Footer -->
            <div class="p-3.5 bg-stone-50 border-t border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-stone-500">
                <span>Hanya pembayaran yang disetujui yang menambah saldo pembukuan kas kelas</span>
                @if($payments->hasPages())
                    <div>{{ $payments->links() }}</div>
                @endif
            </div>
        </div>

        <!-- MODAL: Tolak Pembayaran (Wajib Alasan Penolakan) -->
        <div x-show="rejectModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-xs"
             @keydown.escape.window="rejectModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200" 
                 @click.away="rejectModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        <h3 class="font-semibold text-stone-900 text-sm" x-text="'Tolak Pembayaran - ' + rejectData.name"></h3>
                    </div>
                    <button type="button" @click="rejectModalOpen = false" class="text-stone-400 hover:text-stone-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/bendahara/payments/' + rejectData.id + '/reject'" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-stone-700 font-medium mb-1">
                            Alasan Penolakan <span class="text-rose-600 font-bold">*</span>
                        </label>
                        <textarea name="rejection_reason" 
                                  rows="3" 
                                  class="w-full bg-stone-50 border border-stone-200 rounded-lg p-3 text-xs text-stone-800 focus:outline-none focus:border-stone-400" 
                                  placeholder="Contoh: Bukti transfer buram/tidak terbaca, nominal tidak sesuai, dsb."
                                  required></textarea>
                        <p class="text-[10px] text-stone-400 mt-1">Alasan akan dibaca langsung oleh mahasiswa terkait agar dapat mengirim ulang bukti yang valid.</p>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-rose-700 hover:bg-rose-800 transition shadow-2xs cursor-pointer">
                            Konfirmasi Tolak Pembayaran
                        </button>
                        <button type="button" @click="rejectModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Catat Setoran Kas Tunai Langsung -->
        <div x-show="cashModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-xs"
             @keydown.escape.window="cashModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="cashModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-stone-900"></span>
                        <h3 class="font-semibold text-stone-900 text-sm">Catat Setoran Kas Tunai</h3>
                    </div>
                    <button type="button" @click="cashModalOpen = false" class="text-stone-400 hover:text-stone-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if($unpaidDues->count() > 0)
                <form method="POST" action="{{ route('bendahara.cash-payments.store') }}" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Pilih Mahasiswa &amp; Pekan Kas Tertunggak</label>
                        <select name="student_due_id" 
                                class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" 
                                required>
                            <option value="">-- Pilih Mahasiswa &amp; Pekan Kas --</option>
                            @foreach($unpaidDues as $due)
                                <option value="{{ $due->id }}">
                                    {{ $due->user->name ?? 'Mahasiswa' }} ({{ $due->user->nim ?? '-' }}) &bull; {{ $due->cashPeriod->name ?? ('Pekan ' . $due->cashPeriod->week_number) }} &bull; Rp {{ number_format($due->amount, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-stone-400 mt-1">Hanya menampilkan mahasiswa aktif dengan kewajiban kas yang belum lunas.</p>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-lg space-y-1 text-stone-600 text-[11px]">
                        <div class="font-semibold text-stone-800 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-stone-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Aturan Setoran Kas Tunai:</span>
                        </div>
                        <p>Setoran tunai langsung diverifikasi lunas secara otomatis tanpa memerlukan berkas bukti fisik.</p>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                            Simpan Setoran Tunai
                        </button>
                        <button type="button" @click="cashModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
                @else
                <div class="py-6 text-center text-stone-400">
                    <p class="text-xs">Tidak ada tunggakan kas mahasiswa yang belum lunas.</p>
                </div>
                @endif
            </div>
        </div>

    </div>

</x-layouts.app>
