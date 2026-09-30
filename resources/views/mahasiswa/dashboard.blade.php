<x-layouts.app role="mahasiswa" title="Dashboard Mahasiswa">

    <div x-data="{
        copiedBca: false,
        qrisModalOpen: false,
        copyBca() {
            navigator.clipboard.writeText('8730192819');
            this.copiedBca = true;
            setTimeout(() => this.copiedBca = false, 2500);
        }
    }">

    <!-- Header Greeting & Student Context -->
    <div class="mb-6 pb-4 border-b border-zinc-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Halo, {{ $user->name }}</h2>
                <p class="text-xs text-zinc-500 mt-0.5">
                    NIM: <span class="font-mono text-zinc-700 font-medium">{{ $user->nim ?? '-' }}</span> &bull; Kelas: <span class="text-zinc-700 font-medium">TI26A3 &bull; Teknik Informatika</span>
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                @if($activePeriod)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-medium bg-zinc-100 text-zinc-700 border border-zinc-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        <span>{{ $activePeriod->academic_year }} &bull; Semester {{ ucfirst($activePeriod->semester) }}</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Focus Section: Current Weekly Obligation (Primary Focus) & Personal Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-8">
        
        <!-- Primary Focus: Current Weekly Obligation (lg:col-span-8) -->
        <div id="iuran-mingguan" class="lg:col-span-8 bg-white p-6 rounded-xl border border-zinc-200 flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-zinc-100 gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Kewajiban Pekan Berjalan</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                Pekan Aktif
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 tracking-tight mt-1">
                            {{ $activePeriod->name ?? 'Pekan Kas' }}
                            @if($activePeriod)
                                <span class="font-normal text-zinc-500 text-xs">({{ $activePeriod->academic_year }} &bull; Smt {{ ucfirst($activePeriod->semester) }})</span>
                            @endif
                        </h3>
                    </div>

                    <!-- Clear Primary Status Indicator -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if($currentDue && $currentDue->isPaid())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Lunas</span>
                            </span>
                            @if($pastUnpaidWeeksCount > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-rose-50 text-rose-800 border border-rose-200">
                                    {{ $pastUnpaidWeeksCount }} Pekan Lampau Belum Lunas
                                </span>
                            @endif
                        @elseif($currentDue && $currentDue->pendingPayment)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-900 border border-amber-200">
                                <svg class="w-3.5 h-3.5 text-amber-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Menunggu Verifikasi</span>
                            </span>
                        @elseif($currentDue && $currentDue->rejectedPayment)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-900 border border-rose-200">
                                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Ditolak</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Belum Bayar</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Rejection Notice Banner (if any) -->
                @if($currentDue && $currentDue->rejectedPayment && ! $currentDue->isPaid())
                <div class="mt-4 p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-950 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <span class="font-semibold text-rose-900 block">Bukti transfer ditolak oleh bendahara</span>
                            <p class="text-rose-800 mt-0.5">Alasan: <strong class="underline decoration-rose-300">{{ $currentDue->rejectedPayment->rejection_reason }}</strong></p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="$dispatch('open-payment-modal', { due_id: {{ $currentDue->id }} })"
                            class="px-3 py-1.5 rounded-md bg-rose-700 hover:bg-rose-800 text-white font-semibold text-xs transition shrink-0 cursor-pointer self-start sm:self-auto">
                        Unggah Ulang Bukti &rarr;
                    </button>
                </div>
                @endif

                <!-- 2 Key Data Points: Nominal & Due Date -->
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-zinc-50 p-4 rounded-lg border border-zinc-100">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Nominal Iuran Pekan Ini</span>
                        <span class="text-2xl font-bold font-mono text-zinc-900 mt-1 block">
                            Rp {{ number_format($currentDue?->amount ?? ($activePeriod?->amount ?? 5000), 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-zinc-400 mt-1 block">
                            @if($currentDue && $currentDue->isPaid())
                                Pembayaran telah tercatat sah di kas kelas
                            @elseif($currentDue && $currentDue->pendingPayment)
                                Bukti transfer sedang dalam antrean verifikasi
                            @elseif($currentDue && $currentDue->rejectedPayment)
                                Mohon unggah ulang bukti yang sesuai
                            @else
                                Jatuh tempo setiap pekan perkuliahan
                            @endif
                        </span>
                    </div>

                    <div class="bg-zinc-50 p-4 rounded-lg border border-zinc-100">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Batas Waktu Pembayaran</span>
                        <span class="text-base font-semibold text-zinc-800 mt-1 block">
                            {{ $activePeriod?->due_date ? $activePeriod->due_date->format('d M Y') : 'Sesuai jadwal' }}
                        </span>
                        <span class="text-[11px] text-zinc-400 mt-1 block">
                            {{ $activePeriod?->due_date ? 'Hari ' . $activePeriod->due_date->translatedFormat('l') : 'Sesuai jadwal perkuliahan' }}
                        </span>
                    </div>
                </div>

                <!-- Contextual Guidance -->
                <p class="text-xs text-zinc-500 mt-4 leading-relaxed">
                    @if($currentDue && $currentDue->isPaid())
                        Kewajiban kas Anda untuk <strong>{{ $activePeriod->name ?? 'pekan ini' }}</strong> telah diverifikasi lunas.
                        @if($pastUnpaidWeeksCount > 0)
                            Namun, Anda masih memiliki <strong>{{ $pastUnpaidWeeksCount }} pekan lampau</strong> yang belum disetor.
                        @else
                            Terima kasih atas kedisiplinan dan partisipasi Anda.
                        @endif
                    @elseif($currentDue && $currentDue->pendingPayment)
                        Bukti setoran iuran kas untuk <strong>{{ $activePeriod->name ?? 'pekan ini' }}</strong> sudah diterima dan segera diperiksa oleh bendahara.
                    @elseif($currentDue && $currentDue->rejectedPayment)
                        Bukti setoran untuk <strong>{{ $activePeriod->name ?? 'pekan ini' }}</strong> sebelumnya belum dapat diterima. Silakan cek alasan penolakan dan unggah ulang bukti.
                    @elseif($activePeriod)
                        Iuran kas kelas untuk <strong>{{ $activePeriod->name }}</strong> belum lunas. Silakan lakukan pembayaran via Transfer BCA atau QRIS.
                    @else
                        Belum ada periode kas yang aktif saat ini.
                    @endif
                </p>
            </div>

            <!-- Prominent Payment Action Button -->
            <div class="mt-6 pt-4 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                @if($currentDue && $currentDue->isPaid())
                    @if($unpaidDues->count() > 0)
                        <button type="button" 
                                @click="$dispatch('open-payment-modal', {})" 
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Bayar Iuran Pekan Lain (Sisa {{ $unpaidDues->count() }} Pekan)</span>
                        </button>
                    @else
                        <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-semibold text-xs">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Seluruh Kewajiban Iuran Semester Ini Telah Lunas</span>
                        </div>
                    @endif
                @elseif($currentDue && $currentDue->pendingPayment)
                    <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 font-semibold text-xs">
                        <svg class="w-4 h-4 text-amber-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Bukti Pembayaran Pekan Ini Sedang Diverifikasi</span>
                    </div>
                @else
                    <button type="button" 
                            @click="$dispatch('open-payment-modal', { due_id: {{ $currentDue?->id ?? 'null' }} })" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>{{ $currentDue && $currentDue->rejectedPayment ? 'Unggah Ulang Bukti Pekan Ini' : 'Bayar Iuran Pekan Ini Sekarang' }}</span>
                    </button>
                @endif

                <div class="text-[11px] text-zinc-400 text-center sm:text-right shrink-0">
                    BCA: <span class="text-zinc-700 font-mono font-medium">873-019-2819</span> &bull; QRIS Kas Kelas
                </div>
            </div>
        </div>

        <!-- Secondary Summary: Personal Obligations Across Semester (lg:col-span-4) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-xl border border-zinc-200 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Ringkasan Kewajiban Kas</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <div>
                        <h4 class="text-2xl font-bold font-mono text-zinc-900">
                            Rp {{ number_format($unpaidAmount, 0, ',', '.') }}
                        </h4>
                        @if($unpaidAmount == 0)
                            <span class="text-xs font-semibold text-emerald-700 mt-0.5 block">Bebas Tunggakan</span>
                        @else
                            <span class="text-xs font-semibold text-rose-600 mt-0.5 block">{{ $unpaidDues->count() }} Pekan Belum Lunas</span>
                        @endif
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-medium {{ $unpaidAmount == 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                        {{ $unpaidAmount == 0 ? 'Tertib' : 'Tertunggak' }}
                    </span>
                </div>

                <!-- Semester Progress Bar -->
                <div class="mt-6 pt-4 border-t border-zinc-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-zinc-600 font-medium">Progres Pelunasan Semester</span>
                        <span class="font-mono font-medium text-zinc-800">
                            {{ $paidWeeksCount }} / {{ $totalWeeksCount }} Pekan ({{ $progressPercent }}%)
                        </span>
                    </div>
                    <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-600 rounded-full transition-all duration-300" style="width: {{ min($progressPercent, 100) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-zinc-400 pt-1">
                        <span>Disetor: <strong class="text-zinc-700 font-mono">Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</strong></span>
                        <span>Sisa: <strong class="text-zinc-700 font-mono">{{ max(0, $totalWeeksCount - $paidWeeksCount) }} Pekan</strong></span>
                    </div>
                </div>
            </div>

            <!-- Quick Link to Full Dues Schedule -->
            <div class="mt-6 pt-4 border-t border-zinc-100">
                <a href="{{ route('mahasiswa.iuran.index') }}" class="w-full flex items-center justify-between p-2 rounded-lg bg-zinc-50 hover:bg-zinc-100 text-zinc-700 text-xs font-medium transition">
                    <span>Lihat Jadwal Seluruh Pekan</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </div>

    </div>

    <!-- Quick Payment & Class Account Strip (Akses Cepat Pembayaran & QRIS) -->
    <div class="mb-8 p-4 sm:p-5 bg-white rounded-xl border border-zinc-200 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-zinc-900 tracking-tight">Saluran Pembayaran Resmi Kas Kelas</h4>
                    <p class="text-xs text-zinc-500 mt-0.5">Transfer Bank BCA &bull; QRIS Standar Nasional &bull; Bukti langsung diunggah di KASMA</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Salin No. Rekening BCA -->
                <div class="flex items-center gap-2 bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs">
                    <span class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">BCA</span>
                    <span class="font-mono font-bold text-zinc-900">873-019-2819</span>
                    <button type="button" 
                            @click="copyBca()" 
                            class="ml-1 text-[11px] font-semibold px-2 py-0.5 rounded bg-white hover:bg-zinc-100 border border-zinc-200 text-zinc-700 transition cursor-pointer">
                        <span x-text="copiedBca ? 'Tersalin!' : 'Salin Rekening'"></span>
                    </button>
                </div>

                <!-- Tampilkan QRIS Cepat -->
                <button type="button" 
                        @click="qrisModalOpen = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-800 bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span>Tampilkan QRIS</span>
                </button>

                <!-- Tombol Setor Langsung -->
                <button type="button" 
                        @click="$dispatch('open-payment-modal', {})"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Bayar Kas Sekarang</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Lower Section: Recent Payment History & Simple Class Financial Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Recent Personal Payment History (lg:col-span-8) -->
        <div id="riwayat-pembayaran" class="lg:col-span-8 bg-white rounded-xl border border-zinc-200 overflow-hidden">
            <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-zinc-900 text-sm tracking-tight">Riwayat Pembayaran Kas Saya</h3>
                    <p class="text-xs text-zinc-500">Catatan setoran iuran dan status verifikasi bendahara</p>
                </div>
                <a href="{{ route('mahasiswa.riwayat.index') }}" class="text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold border-b border-zinc-100">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Pekan / Keterangan</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="py-3 px-4 font-mono text-zinc-500 whitespace-nowrap">
                                    {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : $payment->created_at->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-medium text-zinc-900 block">
                                        {{ $payment->studentDue?->cashPeriod?->name ?? 'Iuran Kas' }}
                                    </span>
                                    <span class="text-[10px] text-zinc-400">
                                        {{ $payment->studentDue?->cashPeriod ? 'Semester ' . ucfirst($payment->studentDue->cashPeriod->semester) . ' ' . $payment->studentDue->cashPeriod->academic_year : 'Iuran Kas' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-zinc-700 whitespace-nowrap">
                                    @if($payment->payment_method === 'cash')
                                        Tunai
                                    @elseif($payment->payment_method === 'bank_transfer')
                                        Transfer BCA
                                    @elseif($payment->payment_method === 'qris')
                                        QRIS Kas
                                    @else
                                        {{ ucfirst($payment->payment_method) }}
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-semibold text-zinc-900 whitespace-nowrap">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if($payment->status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    @elseif($payment->status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200" title="{{ $payment->rejection_reason }}">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
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
                                                class="text-[11px] text-zinc-600 hover:text-zinc-900 bg-zinc-100 hover:bg-zinc-200 px-2 py-0.5 rounded transition cursor-pointer">
                                            Lihat
                                        </button>
                                    @else
                                        <span class="text-zinc-300 font-mono">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-zinc-400">
                                    Belum ada catatan setoran pembayaran kas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Simple Class Financial Transparency (lg:col-span-4) -->
        <div id="keuangan-kelas" class="lg:col-span-4 space-y-4">
            
            <div class="bg-white p-5 rounded-xl border border-zinc-200">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-zinc-100">
                    <div>
                        <h4 class="font-semibold text-zinc-900 text-sm">Transparansi Kas Kelas</h4>
                        <p class="text-xs text-zinc-500">Ringkasan kas kelas TI26A3</p>
                    </div>
                    <span class="text-[10px] font-medium px-2 py-0.5 rounded bg-zinc-100 text-zinc-600 border border-zinc-200">Terbuka</span>
                </div>

                <!-- Saldo Kas -->
                <div class="bg-zinc-50 p-4 rounded-lg border border-zinc-100 mb-3">
                    <span class="text-[10px] text-zinc-500 uppercase tracking-wider block">Sisa Saldo Kas Kelas</span>
                    <span class="text-2xl font-bold font-mono text-zinc-900 block mt-0.5">
                        Rp {{ number_format($currentBalance, 0, ',', '.') }}
                    </span>
                    <div class="grid grid-cols-2 gap-2 mt-3 pt-2.5 border-t border-zinc-200 text-xs">
                        <div>
                            <span class="text-[10px] text-zinc-400 uppercase block">Pemasukan</span>
                            <span class="font-bold font-mono text-emerald-700 text-xs">
                                +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-zinc-400 uppercase block">Pengeluaran</span>
                            <span class="font-bold font-mono text-zinc-800 text-xs">
                                -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-1 text-center">
                    <a href="{{ route('mahasiswa.keuangan.index') }}" class="text-xs font-medium text-zinc-600 hover:text-zinc-900 underline inline-flex items-center gap-1">
                        <span>Buka Rincian Arus Kas &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Bendahara Contact Box -->
            <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-200 text-xs">
                <p class="font-semibold text-zinc-900 mb-0.5">Pertanyaan Soal Iuran Kas?</p>
                <p class="text-zinc-500 text-[11px] mb-3">Hubungi bendahara kelas untuk konfirmasi bukti atau permohonan dispensasi.</p>
                <div class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-zinc-200">
                    <div>
                        <p class="font-medium text-zinc-800 text-xs">{{ $bendaharaContact->name ?? 'Bendahara Kelas' }}</p>
                        <p class="text-[10px] text-zinc-400 font-mono">{{ $bendaharaContact->phone_number ?? '0821-9876-5432' }}</p>
                    </div>
                    <a href="https://wa.me/{{ \App\Models\User::formatWhatsappNumber($bendaharaContact->phone_number ?? '6282198765432') }}?text={{ urlencode('Halo Bendahara KASMA, saya ' . $user->name . ' (NIM ' . ($user->nim ?? '-') . ') ingin konfirmasi terkait iuran kas.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="text-[11px] text-emerald-700 hover:text-emerald-900 font-medium bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-1 rounded transition cursor-pointer">
                        WhatsApp &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal Preview QRIS Cepat -->
    <div x-show="qrisModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
         @keydown.escape.window="qrisModalOpen = false">
        <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-xl border border-zinc-200" 
             @click.away="qrisModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <h3 class="font-semibold text-zinc-900 text-sm">QRIS Kas Kelas TI26A3</h3>
                </div>
                <button type="button" @click="qrisModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-4 text-center space-y-3">
                <div class="p-3 bg-zinc-900 text-white rounded-lg inline-block">
                    <div class="bg-white p-3 rounded-md">
                        <svg class="w-44 h-44 mx-auto" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="100" height="100" fill="white"/>
                            <rect x="6" y="6" width="28" height="28" fill="#18181b" rx="2"/>
                            <rect x="11" y="11" width="18" height="18" fill="white" rx="1"/>
                            <rect x="15" y="15" width="10" height="10" fill="#18181b"/>
                            <rect x="66" y="6" width="28" height="28" fill="#18181b" rx="2"/>
                            <rect x="71" y="11" width="18" height="18" fill="white" rx="1"/>
                            <rect x="75" y="15" width="10" height="10" fill="#18181b"/>
                            <rect x="6" y="66" width="28" height="28" fill="#18181b" rx="2"/>
                            <rect x="11" y="71" width="18" height="18" fill="white" rx="1"/>
                            <rect x="15" y="75" width="10" height="10" fill="#18181b"/>
                            <rect x="38" y="10" width="6" height="6" fill="#18181b"/>
                            <rect x="48" y="10" width="6" height="6" fill="#18181b"/>
                            <rect x="38" y="20" width="6" height="6" fill="#18181b"/>
                            <rect x="52" y="20" width="6" height="6" fill="#18181b"/>
                            <rect x="42" y="30" width="6" height="6" fill="#18181b"/>
                            <rect x="10" y="42" width="6" height="6" fill="#18181b"/>
                            <rect x="20" y="42" width="6" height="6" fill="#18181b"/>
                            <rect x="30" y="42" width="6" height="6" fill="#18181b"/>
                            <rect x="42" y="42" width="16" height="16" fill="#18181b" rx="1"/>
                            <rect x="66" y="42" width="6" height="6" fill="#18181b"/>
                            <rect x="76" y="42" width="14" height="6" fill="#18181b"/>
                            <rect x="10" y="52" width="8" height="6" fill="#18181b"/>
                            <rect x="24" y="52" width="12" height="6" fill="#18181b"/>
                            <rect x="66" y="52" width="8" height="6" fill="#18181b"/>
                            <rect x="80" y="52" width="10" height="6" fill="#18181b"/>
                            <rect x="38" y="66" width="6" height="6" fill="#18181b"/>
                            <rect x="48" y="66" width="10" height="6" fill="#18181b"/>
                            <rect x="66" y="66" width="6" height="6" fill="#18181b"/>
                            <rect x="76" y="66" width="6" height="6" fill="#18181b"/>
                            <rect x="86" y="66" width="8" height="6" fill="#18181b"/>
                            <rect x="38" y="78" width="12" height="6" fill="#18181b"/>
                            <rect x="56" y="78" width="6" height="6" fill="#18181b"/>
                            <rect x="68" y="78" width="8" height="6" fill="#18181b"/>
                            <rect x="82" y="78" width="12" height="6" fill="#18181b"/>
                            <rect x="42" y="88" width="8" height="6" fill="#18181b"/>
                            <rect x="56" y="88" width="10" height="6" fill="#18181b"/>
                            <rect x="72" y="88" width="8" height="6" fill="#18181b"/>
                            <rect x="86" y="88" width="8" height="6" fill="#18181b"/>
                        </svg>
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-2 font-mono">NMID: ID1026002910</p>
                </div>

                <div class="text-xs text-zinc-600">
                    <p class="font-semibold text-zinc-900">Mendukung Seluruh Aplikasi:</p>
                    <p class="text-[11px] text-zinc-500">GoPay, OVO, Dana, ShopeePay, BCA, Mandiri, BRI, BNI, dll.</p>
                </div>

                <div class="pt-2 border-t border-zinc-100 flex gap-2">
                    <button type="button" 
                            @click="qrisModalOpen = false; $dispatch('open-payment-modal', {})"
                            class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 transition cursor-pointer">
                        Unggah Bukti Bayar &rarr;
                    </button>
                    <button type="button" @click="qrisModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    </div>

</x-layouts.app>
