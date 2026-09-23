<x-layouts.app role="mahasiswa" title="Riwayat Pembayaran Kas">

    <div x-data="{
        filter: 'semua'
    }">

        <!-- Header & Action -->
        <div class="mb-6 pb-4 border-b border-stone-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-stone-900 tracking-tight">Riwayat Pembayaran Kas Saya</h2>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Lacak seluruh status setoran iuran kas mingguan, bukti transfer, dan catatan verifikasi bendahara.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="$dispatch('open-payment-modal')"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-white bg-emerald-800 hover:bg-emerald-900 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Setor Iuran Kas</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Terverifikasi</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-emerald-800 mt-2 block">
                    Rp {{ number_format($totalApprovedAmount, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">
                    {{ $approvedCount }} transaksi disetujui masuk kas
                </span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Menunggu Verifikasi</span>
                    <span class="w-2 h-2 rounded-full {{ $pendingCount > 0 ? 'bg-amber-500' : 'bg-stone-300' }}"></span>
                </div>
                <span class="text-2xl sm:text-3xl font-bold font-mono {{ $pendingCount > 0 ? 'text-amber-900' : 'text-stone-900' }} mt-2 block">
                    {{ $pendingCount }} Setoran
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Sedang diperiksa oleh bendahara</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Pembayaran Ditolak</span>
                    <span class="w-2 h-2 rounded-full {{ $rejectedCount > 0 ? 'bg-rose-500' : 'bg-stone-300' }}"></span>
                </div>
                <span class="text-2xl sm:text-3xl font-bold font-mono {{ $rejectedCount > 0 ? 'text-rose-700' : 'text-stone-900' }} mt-2 block">
                    {{ $rejectedCount }} Transaksi
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Perlu dikirim ulang bukti valid</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Sisa Tunggakan Belum Bayar</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono {{ $unpaidDues->count() > 0 ? 'text-rose-700' : 'text-stone-900' }} mt-2 block">
                    {{ $unpaidDues->count() }} Pekan
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">
                    {{ $unpaidDues->count() == 0 ? 'Bebas tunggakan' : 'Belum disetorkan ke kasir' }}
                </span>
            </div>
        </div>

        <!-- Filter Pills & Payments List -->
        <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-stone-900 text-base tracking-tight">Daftar Transaksi Pembayaran</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Riwayat setoran digital dan tunai beserta status verifikasi bendahara</p>
                </div>

                <!-- Filter Controls -->
                <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-lg border border-stone-200/80 text-xs">
                    <button type="button" 
                            @click="filter = 'semua'" 
                            :class="filter === 'semua' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Semua ({{ $payments->count() }})
                    </button>
                    <button type="button" 
                            @click="filter = 'pending'" 
                            :class="filter === 'pending' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Menunggu ({{ $pendingCount }})
                    </button>
                    <button type="button" 
                            @click="filter = 'approved'" 
                            :class="filter === 'approved' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Disetujui ({{ $approvedCount }})
                    </button>
                    <button type="button" 
                            @click="filter = 'rejected'" 
                            :class="filter === 'rejected' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Ditolak ({{ $rejectedCount }})
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50/90 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                        <tr>
                            <th class="py-3 px-4">Pekan Kas</th>
                            <th class="py-3 px-4">Tanggal Pembayaran</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Catatan / Keterangan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($payments as $p)
                            @php
                                $status = $p->status;
                                $periodName = $p->studentDue?->cashPeriod?->name ?? 'Pekan Kas';
                                $dueId = $p->student_due_id;
                            @endphp
                            <tr class="hover:bg-stone-50/50 transition"
                                x-show="filter === 'semua' || filter === '{{ $status }}'">
                                <td class="py-3.5 px-4 font-semibold text-stone-900 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $p->isApproved() ? 'bg-emerald-600' : ($p->isPending() ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                        <span>{{ $periodName }}</span>
                                    </div>
                                    <span class="text-[10px] text-stone-400 font-normal block pl-4">
                                        {{ $p->studentDue?->cashPeriod?->academic_year ?? '' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap text-stone-700">
                                    <span class="font-mono">{{ $p->payment_date ? $p->payment_date->format('d M Y, H:i') : $p->created_at->format('d M Y, H:i') }}</span>
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
                                            <span>Menunggu Verifikasi</span>
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
                                <td class="py-3.5 px-4 max-w-xs">
                                    @if($p->isRejected())
                                        <div class="p-2 rounded bg-rose-50/70 border border-rose-200/70 text-rose-800 text-[11px] leading-tight">
                                            <strong class="font-semibold block text-rose-900 mb-0.5">Alasan Penolakan:</strong>
                                            <span>{{ $p->rejection_reason ?? 'Bukti transfer tidak valid atau nominal tidak sesuai.' }}</span>
                                        </div>
                                    @elseif($p->isApproved())
                                        <span class="text-stone-500 text-[11px]">
                                            Diverifikasi oleh {{ $p->verifier->name ?? 'Bendahara' }} pada {{ $p->verified_at ? $p->verified_at->format('d M Y, H:i') : '-' }}
                                        </span>
                                    @else
                                        <span class="text-stone-400 text-[11px]">Menunggu bendahara memeriksa bukti transfer.</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($p->proof_file_path)
                                            <button type="button" 
                                                    @click="openProof({
                                                        name: '{{ addslashes($periodName) }}',
                                                        amount: 'Rp {{ number_format($p->amount, 0, ',', '.') }}',
                                                        method: '{{ $p->payment_method === 'qris' ? 'QRIS Kas' : 'Transfer BCA' }}',
                                                        time: '{{ $p->payment_date ? $p->payment_date->format('d M Y, H:i') : $p->created_at->format('d M Y, H:i') }}',
                                                        weeks: '{{ $periodName }}',
                                                        proof_url: '{{ route('payments.proof', $p) }}',
                                                        payment_id: {{ $p->id }},
                                                        is_pending: false
                                                    })" 
                                                    class="py-1 px-2.5 text-[11px] font-medium rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition cursor-pointer">
                                                Cek Bukti
                                            </button>
                                        @endif

                                        @if($p->isRejected() && $p->studentDue?->isUnpaid() && $p->studentDue?->pendingPayment === null)
                                            <button type="button" 
                                                    @click="$dispatch('open-payment-modal', { due_id: {{ $dueId }} })"
                                                    class="py-1 px-2.5 text-[11px] font-semibold rounded bg-rose-700 hover:bg-rose-800 text-white transition shadow-2xs cursor-pointer">
                                                Kirim Ulang Bukti &rarr;
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400">
                                    <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                    <p class="font-medium text-stone-600 text-sm">Belum Ada Riwayat Pembayaran</p>
                                    <p class="text-xs text-stone-400 mt-1">Anda belum melakukan setoran iuran kas kelas.</p>
                                    <button type="button" 
                                            @click="$dispatch('open-payment-modal')"
                                            class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-emerald-800 hover:bg-emerald-900 transition cursor-pointer">
                                        <span>Setor Kas Sekarang</span>
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer note -->
            <div class="p-3.5 bg-stone-50 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                <span>Setoran yang disetujui tercatat resmi dalam pembukuan kas kelas</span>
                <span class="font-mono">Total Transaksi: {{ $payments->count() }}</span>
            </div>
        </div>

    </div>

</x-layouts.app>
