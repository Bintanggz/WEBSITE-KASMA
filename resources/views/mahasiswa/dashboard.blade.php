<x-layouts.app role="mahasiswa" title="Dashboard Mahasiswa">

    <!-- Header Greeting & Student Context -->
    <div class="mb-6 pb-4 border-b border-stone-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-stone-900 tracking-tight">Halo, {{ $user->name }}</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                    NIM: <span class="font-mono text-stone-700 font-semibold">{{ $user->nim ?? '-' }}</span> &bull; Kelas: <span class="text-stone-700 font-medium">TI26A3 &bull; Teknik Informatika</span>
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                @if($activePeriod)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-medium bg-stone-100 text-stone-700 border border-stone-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        <span>{{ $activePeriod->academic_year }} &bull; Semester {{ ucfirst($activePeriod->semester) }}</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Focus Section: Current Week Obligation (Primary Focus) & Personal Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-8">
        
        <!-- Primary Focus: Current Weekly Obligation (lg:col-span-8) -->
        <div id="iuran-mingguan" class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-xl border border-stone-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-stone-100 gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Kewajiban Pekan Berjalan</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                Pekan Aktif
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 tracking-tight mt-1">
                            {{ $activePeriod->name ?? 'Pekan Kas' }}
                            @if($activePeriod)
                                <span class="font-normal text-stone-500 text-sm">({{ $activePeriod->academic_year }} &bull; Semester {{ ucfirst($activePeriod->semester) }})</span>
                            @endif
                        </h3>
                    </div>

                    <!-- Clear Primary Status Pill & Cumulative Warning -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if($currentDue && $currentDue->isPaid())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Status: Lunas</span>
                            </span>
                            @if($pastUnpaidWeeksCount > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200" title="Anda memiliki pekan sebelum pekan ini yang belum disetor">
                                    <span>&bull; {{ $pastUnpaidWeeksCount }} Pekan Lampau Tertunggak</span>
                                </span>
                            @endif
                        @elseif($currentDue && $currentDue->pendingPayment)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200/80">
                                <svg class="w-4 h-4 text-amber-700 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Menunggu Verifikasi</span>
                            </span>
                        @elseif($currentDue && $currentDue->rejectedPayment)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-900 border border-rose-300">
                                <svg class="w-4 h-4 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Setoran Ditolak</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200/80">
                                <svg class="w-4 h-4 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Belum Lunas</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Rejection Notice Banner (if any) -->
                @if($currentDue && $currentDue->rejectedPayment && ! $currentDue->isPaid())
                <div class="mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-950 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs shadow-2xs">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <span class="font-bold text-rose-900 block">Bukti Transfer Anda Ditolak oleh Bendahara</span>
                            <p class="text-rose-800 mt-0.5">Catatan: <strong class="underline decoration-rose-300">{{ $currentDue->rejectedPayment->rejection_reason }}</strong></p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="$dispatch('open-payment-modal', { due_id: {{ $currentDue->id }} })"
                            class="px-3.5 py-1.5 rounded-lg bg-rose-700 hover:bg-rose-800 text-white font-semibold text-xs transition shrink-0 cursor-pointer self-start sm:self-auto shadow-2xs">
                        Unggah Ulang Bukti &rarr;
                    </button>
                </div>
                @endif

                <!-- 2 Key Data Points: Nominal & Due Date -->
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
                        <span class="text-xs text-stone-500 block">Nominal Iuran Pekan Ini</span>
                        <span class="text-2xl font-bold font-mono text-stone-900 mt-1 block">
                            Rp {{ number_format($currentDue?->amount ?? ($activePeriod?->amount ?? 10000), 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-stone-400 mt-0.5 block">
                            @if($currentDue && $currentDue->isPaid())
                                &bull; Pembayaran telah tercatat sah di kas kelas
                            @elseif($currentDue && $currentDue->pendingPayment)
                                &bull; Bukti sedang diperiksa oleh bendahara
                            @elseif($currentDue && $currentDue->rejectedPayment)
                                &bull; Bukti sebelumnya ditolak, silakan kirim ulang
                            @else
                                &bull; Menunggu pembayaran sebelum jatuh tempo
                            @endif
                        </span>
                    </div>

                    <div class="bg-stone-50 p-4 rounded-lg border border-stone-100">
                        <span class="text-xs text-stone-500 block">Batas Waktu Pembayaran</span>
                        <span class="text-base font-semibold text-stone-800 mt-1.5 block">
                            {{ $activePeriod?->due_date ? $activePeriod->due_date->format('d M Y') : 'Sesuai jadwal' }}
                        </span>
                        <span class="text-[11px] text-stone-400 mt-0.5 block">
                            {{ $activePeriod?->due_date ? 'Batas akhir hari ' . $activePeriod->due_date->translatedFormat('l') : 'Sesuai jadwal perkuliahan' }}
                        </span>
                    </div>
                </div>

                <!-- Contextual Guidance -->
                <p class="text-xs text-stone-500 mt-4 leading-relaxed">
                    @if($currentDue && $currentDue->isPaid())
                        Kewajiban kas Anda untuk <strong>{{ $activePeriod->name ?? 'pekan ini' }}</strong> telah diverifikasi lunas.
                        @if($pastUnpaidWeeksCount > 0)
                            Namun, mohon diperhatikan bahwa Anda masih memiliki <strong>{{ $pastUnpaidWeeksCount }} pekan lampau</strong> yang belum disetor.
                        @else
                            Terima kasih atas partisipasi aktif Anda dalam ketertiban kas kelas.
                        @endif
                    @elseif($currentDue && $currentDue->pendingPayment)
                        Bukti setoran iuran kas untuk <strong>{{ $activePeriod->name ?? 'pekan ini' }}</strong> sudah kami terima. Bendahara akan segera memverifikasinya.
                    @elseif($currentDue && $currentDue->rejectedPayment)
                        Bukti setoran untuk <strong>{{ $activePeriod->name ?? 'pekan ini' }}</strong> sebelumnya belum dapat diterima. Silakan cek alasan penolakan dan unggah bukti baru.
                    @elseif($activePeriod)
                        Iuran kas kelas untuk <strong>{{ $activePeriod->name }}</strong> belum lunas. Silakan lakukan transfer atau setoran dan kirimkan bukti pembayarannya.
                    @else
                        Belum ada periode kas yang aktif saat ini.
                    @endif
                </p>
            </div>

            <!-- Prominent Payment Action Button -->
            <div class="mt-6 pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                @if($currentDue && $currentDue->isPaid())
                    @if($unpaidDues->count() > 0)
                        <button type="button" 
                                @click="$dispatch('open-payment-modal', {})" 
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-800 hover:bg-emerald-900 text-white font-semibold text-xs transition shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Bayar Iuran Pekan Lain (Tersisa {{ $unpaidDues->count() }} Pekan Tertunggak)</span>
                        </button>
                    @else
                        <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold text-xs">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Seluruh Kewajiban Iuran Semester Ini Telah Lunas</span>
                        </div>
                    @endif
                @elseif($currentDue && $currentDue->pendingPayment)
                    <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 font-semibold text-xs">
                        <svg class="w-4 h-4 text-amber-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Bukti Pembayaran Pekan Ini Sedang Menunggu Verifikasi Bendahara</span>
                    </div>
                @else
                    <button type="button" 
                            @click="$dispatch('open-payment-modal', { due_id: {{ $currentDue?->id ?? 'null' }} })" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-emerald-800 hover:bg-emerald-900 text-white font-semibold text-xs transition shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>{{ $currentDue && $currentDue->rejectedPayment ? 'Unggah Ulang Bukti Pekan Ini' : 'Bayar Iuran Pekan Ini Sekarang' }}</span>
                    </button>
                @endif

                <div class="text-[11px] text-stone-500 text-center sm:text-right shrink-0">
                    Rekening Kas: <span class="text-stone-800 font-mono font-medium">BCA 873-019-2819</span> (a.n Bendahara)
                </div>
            </div>
        </div>

        <!-- Secondary Summary: Personal Obligations Across Semester (lg:col-span-4) -->
        <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-xl border border-stone-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Ringkasan Kewajiban Kas</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <div>
                        <h4 class="text-2xl font-bold font-mono text-stone-900">
                            Rp {{ number_format($unpaidAmount, 0, ',', '.') }}
                        </h4>
                        @if($unpaidAmount == 0)
                            <span class="text-xs font-semibold text-emerald-800 mt-0.5 block">Bebas Tunggakan</span>
                        @else
                            <span class="text-xs font-semibold text-rose-700 mt-0.5 block">{{ $unpaidDues->count() }} Pekan Belum Lunas</span>
                        @endif
                    </div>
                    <span class="px-2 py-0.5 rounded text-[11px] font-medium {{ $unpaidAmount == 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-rose-50 text-rose-800 border border-rose-200/60' }}">
                        {{ $unpaidAmount == 0 ? 'Tertib' : 'Tertunggak' }}
                    </span>
                </div>

                <!-- Semester Progress Bar -->
                <div class="mt-6 pt-4 border-t border-stone-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-stone-600 font-medium">Progres Pelunasan Semester</span>
                        <span class="font-mono font-semibold text-stone-900">
                            {{ $paidWeeksCount }} / {{ $totalWeeksCount }} Pekan ({{ $progressPercent }}%)
                        </span>
                    </div>
                    <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-700 rounded-full transition-all duration-300" style="width: {{ min($progressPercent, 100) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-stone-500 pt-1">
                        <span>Disetor: <strong class="text-stone-800 font-mono">Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</strong></span>
                        <span>Sisa: <strong class="text-stone-800 font-mono">{{ max(0, $totalWeeksCount - $paidWeeksCount) }} Pekan</strong></span>
                    </div>
                </div>
            </div>

            <!-- Quick Link to Full Dues Schedule -->
            <div class="mt-6 pt-4 border-t border-stone-100">
                <a href="{{ route('mahasiswa.iuran.index') }}" class="w-full flex items-center justify-between p-2 rounded-lg bg-stone-50 hover:bg-stone-100 text-stone-700 text-xs font-medium transition">
                    <span>Lihat Jadwal Seluruh Pekan</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Lower Section: Recent Payment History & Simple Class Financial Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Recent Personal Payment History (lg:col-span-8) -->
        <div id="riwayat-pembayaran" class="lg:col-span-8 bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-stone-900 text-base tracking-tight">Riwayat Pembayaran Kas Saya</h3>
                    <p class="text-xs text-stone-500">Catatan setoran iuran dan status verifikasi bendahara</p>
                </div>
                <a href="{{ route('mahasiswa.riwayat.index') }}" class="text-xs font-semibold text-stone-700 hover:text-stone-900 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50/80 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Pekan / Keterangan</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                    {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : $payment->created_at->format('d M Y') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-medium text-stone-900 block">
                                        {{ $payment->studentDue?->cashPeriod?->name ?? 'Iuran Kas' }}
                                    </span>
                                    <span class="text-[11px] text-stone-400">
                                        {{ $payment->studentDue?->cashPeriod ? 'Semester ' . ucfirst($payment->studentDue->cashPeriod->semester) . ' ' . $payment->studentDue->cashPeriod->academic_year : 'Iuran Kas Mahasiswa' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-stone-700 whitespace-nowrap">
                                    @if($payment->payment_method === 'cash')
                                        Tunai (Kelas)
                                    @elseif($payment->payment_method === 'bank_transfer')
                                        Transfer BCA
                                    @elseif($payment->payment_method === 'qris')
                                        QRIS Kas
                                    @else
                                        {{ ucfirst($payment->payment_method) }}
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($payment->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                            Disetujui
                                        </span>
                                    @elseif($payment->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/60">
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/60" title="{{ $payment->rejection_reason }}">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($payment->proof_file_path)
                                        <button type="button" 
                                                @click="openProof({
                                                    name: '{{ $user->name }} ({{ $payment->studentDue?->cashPeriod?->name }})',
                                                    amount: 'Rp {{ number_format($payment->amount, 0, ',', '.') }}',
                                                    method: '{{ $payment->payment_method === 'qris' ? 'QRIS Kas' : 'Transfer BCA' }}',
                                                    time: '{{ $payment->payment_date ? $payment->payment_date->format('d M Y, H:i') : '-' }}',
                                                    weeks: '{{ $payment->studentDue?->cashPeriod?->name }}',
                                                    proof_url: '{{ route('payments.proof', $payment) }}',
                                                    payment_id: {{ $payment->id }}
                                                })"
                                                class="text-[11px] text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2.5 py-1 rounded transition cursor-pointer">
                                            Lihat
                                        </button>
                                    @else
                                        <span class="text-stone-400 font-mono">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-stone-400">
                                    Belum ada catatan setoran pembayaran kas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Simple Class Financial Transparency (lg:col-span-4) -->
        <div id="keuangan-kelas" class="lg:col-span-4 space-y-5">
            
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
                    <div>
                        <h4 class="font-semibold text-stone-900 text-sm">Transparansi Kas Kelas</h4>
                        <p class="text-xs text-stone-500">Ringkasan buku besar kelas TI26A3</p>
                    </div>
                    <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200/60">Terbuka</span>
                </div>

                <!-- Saldo Kas -->
                <div class="bg-stone-50 p-4 rounded-lg border border-stone-100 mb-3">
                    <span class="text-[11px] text-stone-500 block">Sisa Saldo Kas Kelas</span>
                    <span class="text-2xl font-bold font-mono text-stone-900 block mt-0.5">
                        Rp {{ number_format($currentBalance, 0, ',', '.') }}
                    </span>
                    <div class="grid grid-cols-2 gap-2 mt-3 pt-2.5 border-t border-stone-200/60 text-xs">
                        <div>
                            <span class="text-[10px] text-stone-400 uppercase block">Pemasukan</span>
                            <span class="font-bold font-mono text-emerald-800 text-xs">
                                +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-stone-400 uppercase block">Pengeluaran</span>
                            <span class="font-bold font-mono text-stone-800 text-xs">
                                -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-2 text-center">
                    <a href="{{ route('mahasiswa.keuangan.index') }}" class="text-xs font-semibold text-stone-700 hover:text-stone-900 underline inline-flex items-center gap-1">
                        <span>Buka Rincian Arus Kas Lengkap &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Bendahara Contact Box -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/80 text-xs">
                <p class="font-semibold text-stone-900 mb-0.5">Pertanyaan Soal Iuran Kas?</p>
                <p class="text-stone-500 text-[11px] mb-3">Hubungi bendahara kelas untuk konfirmasi bukti atau permohonan dispensasi.</p>
                <div class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-stone-200">
                    <div>
                        <p class="font-medium text-stone-800 text-xs">{{ $bendaharaContact->name ?? 'Bendahara Kelas' }}</p>
                        <p class="text-[10px] text-stone-500 font-mono">{{ $bendaharaContact->phone_number ?? '0821-9876-5432' }}</p>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $bendaharaContact->phone_number ?? '6282198765432') }}?text={{ urlencode('Halo Bendahara KASMA, saya ' . $user->name . ' (NIM ' . ($user->nim ?? '-') . ') ingin konfirmasi terkait iuran kas.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="text-[11px] text-emerald-800 hover:text-emerald-950 font-semibold bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded transition cursor-pointer">
                        WhatsApp &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
