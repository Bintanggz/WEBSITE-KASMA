<x-layouts.app role="mahasiswa" title="Riwayat Pembayaran Kas">

    <div x-data="{
        filter: 'semua'
    }">

        <!-- Header & Action -->
        <div class="mb-6 pb-4 border-b border-zinc-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Riwayat Pembayaran Kas Saya</h2>
                    <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                        Lacak seluruh status setoran iuran kas mingguan, bukti transfer, dan catatan verifikasi bendahara.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="$dispatch('open-payment-modal')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Setor Iuran Kas</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Clean Integrated Metric Strip -->
        <div class="bg-white rounded-xl border border-zinc-200 p-5 mb-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 divide-y lg:divide-y-0 lg:divide-x divide-zinc-100">
                <div class="pt-3 lg:pt-0 lg:px-4 first:lg:pl-0">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Disetujui</span>
                    <span class="text-2xl font-bold font-mono text-emerald-800 mt-1 block">
                        Rp {{ number_format($totalApprovedAmount, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">
                        {{ $approvedCount }} transaksi sah masuk kas
                    </span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Menunggu Verifikasi</span>
                        <span class="w-2 h-2 rounded-full {{ $pendingCount > 0 ? 'bg-amber-500' : 'bg-zinc-300' }}"></span>
                    </div>
                    <span class="text-2xl font-bold font-mono {{ $pendingCount > 0 ? 'text-amber-900' : 'text-zinc-900' }} mt-1 block">
                        {{ $pendingCount }} Setoran
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Dalam antrean tinjauan bendahara</span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Pembayaran Ditolak</span>
                        <span class="w-2 h-2 rounded-full {{ $rejectedCount > 0 ? 'bg-rose-500' : 'bg-zinc-300' }}"></span>
                    </div>
                    <span class="text-2xl font-bold font-mono {{ $rejectedCount > 0 ? 'text-rose-600' : 'text-zinc-900' }} mt-1 block">
                        {{ $rejectedCount }} Transaksi
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Perlu unggah ulang bukti yang sah</span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Sisa Tunggakan</span>
                    <span class="text-2xl font-bold font-mono {{ $unpaidDues->count() > 0 ? 'text-rose-600' : 'text-zinc-900' }} mt-1 block">
                        {{ $unpaidDues->count() }} Pekan
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">
                        {{ $unpaidDues->count() == 0 ? 'Bebas tunggakan' : 'Belum disetor ke kasir' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter Pills & Payments List -->
        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
            <div class="p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-zinc-900 text-sm tracking-tight">Daftar Transaksi Pembayaran</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Riwayat setoran digital dan tunai beserta status verifikasi bendahara</p>
                </div>

                <!-- Filter Controls -->
                <div class="flex items-center gap-1 bg-zinc-100 p-1 rounded-lg text-xs">
                    <button type="button" 
                            @click="filter = 'semua'" 
                            :class="filter === 'semua' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Semua ({{ $payments->count() }})
                    </button>
                    <button type="button" 
                            @click="filter = 'pending'" 
                            :class="filter === 'pending' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Menunggu ({{ $pendingCount }})
                    </button>
                    <button type="button" 
                            @click="filter = 'approved'" 
                            :class="filter === 'approved' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Disetujui ({{ $approvedCount }})
                    </button>
                    <button type="button" 
                            @click="filter = 'rejected'" 
                            :class="filter === 'rejected' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Ditolak ({{ $rejectedCount }})
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold border-b border-zinc-100">
                        <tr>
                            <th class="py-3 px-4">Pekan Kas</th>
                            <th class="py-3 px-4">Tanggal Pembayaran</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Catatan / Alasan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($payments as $p)
                            @php
                                $status = $p->status;
                                $periodName = $p->studentDue?->cashPeriod?->name ?? 'Pekan Kas';
                                $dueId = $p->student_due_id;
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition"
                                x-show="filter === 'semua' || filter === '{{ $status }}'">
                                <td class="py-3.5 px-4 font-semibold text-zinc-900 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $p->isApproved() ? 'bg-emerald-600' : ($p->isPending() ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                        <span>{{ $periodName }}</span>
                                    </div>
                                    <span class="text-[10px] text-zinc-400 font-normal block pl-4">
                                        {{ $p->studentDue?->cashPeriod ? 'Semester ' . ucfirst($p->studentDue->cashPeriod->semester) . ' ' . $p->studentDue->cashPeriod->academic_year : 'Iuran Kas' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap font-mono text-zinc-700">
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
                                <td class="py-3.5 px-4 text-zinc-500">
                                    @if($p->isRejected())
                                        <span class="text-rose-700 font-medium text-xs">{{ $p->rejection_reason }}</span>
                                    @elseif($p->isPending())
                                        <span class="text-zinc-400 italic text-[11px]">Sedang diperiksa bendahara</span>
                                    @else
                                        <span class="text-zinc-400 text-[11px]">Pembayaran sah dicatat kasir</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($p->proof_file_path)
                                            <button type="button" 
                                                    @click="openProof({
                                                        name: '{{ $user->name }} ({{ $periodName }})',
                                                        amount: 'Rp {{ number_format($p->amount, 0, ',', '.') }}',
                                                        method: '{{ $p->payment_method === 'qris' ? 'QRIS Kas' : 'Transfer BCA' }}',
                                                        time: '{{ $p->payment_date ? $p->payment_date->format('d M Y, H:i') : '-' }}',
                                                        weeks: '{{ $periodName }}',
                                                        proof_url: '{{ route('payments.proof', $p) }}',
                                                        payment_id: {{ $p->id }}
                                                    })" 
                                                    class="py-1 px-2.5 text-[11px] font-medium rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-700 transition cursor-pointer">
                                                Bukti
                                            </button>
                                        @endif

                                        @if($p->isRejected() && $dueId)
                                            <button type="button" 
                                                    @click="$dispatch('open-payment-modal', { due_id: {{ $dueId }} })"
                                                    class="py-1 px-2.5 text-[11px] font-semibold rounded bg-rose-700 hover:bg-rose-800 text-white transition cursor-pointer">
                                                Kirim Ulang
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 px-4 text-center text-zinc-400">
                                    Belum ada catatan transaksi setoran kas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer info -->
            <div class="p-3.5 bg-zinc-50 border-t border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-zinc-400">
                <span>Catatan pembayaran tersimpan permanen dan transparan</span>
                <button type="button" 
                        @click="$dispatch('open-payment-modal')" 
                        class="text-emerald-700 hover:underline font-semibold cursor-pointer self-start sm:self-auto">
                    Kirim Bukti Pembayaran Baru &rarr;
                </button>
            </div>
        </div>

    </div>

</x-layouts.app>
