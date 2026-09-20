<x-layouts.app role="mahasiswa" title="Dashboard Mahasiswa">

    <!-- Header Greeting & Student Context -->
    <div class="mb-6 pb-4 border-b border-stone-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold text-stone-900 tracking-tight">Halo, {{ $user->name }}</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                    NIM: <span class="font-mono text-stone-700 font-semibold">{{ $user->nim ?? '-' }}</span> &bull; Kelas: <span class="text-stone-700 font-medium">TI-3A (Teknik Informatika)</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if($currentDue && $currentDue->isPaid())
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/80 text-xs font-medium">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Iuran Pekan Ini Lunas</span>
                    </span>
                @elseif($currentDue && $currentDue->pendingPayment)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200/80 text-xs font-medium">
                        <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Menunggu Verifikasi</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-800 border border-rose-200/80 text-xs font-medium">
                        <svg class="w-3.5 h-3.5 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Belum Bayar Pekan Ini</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Focus Section: Current Week Status & Outstanding Payments -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-8">
        
        <!-- Focus 1, 2, & 3: Current Weekly Payment Status, Amount, and Action (md:col-span-7) -->
        <div id="iuran-mingguan" class="md:col-span-7 bg-white p-5 sm:p-6 rounded-xl border border-stone-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Pekan Berjalan</span>
                        <h3 class="text-lg font-bold text-stone-900 tracking-tight mt-0.5">
                            {{ $activePeriod->name ?? 'Pekan Aktif' }} (Semester Genap)
                        </h3>
                    </div>
                    @if($currentDue && $currentDue->isPaid())
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                            Status: Lunas
                        </span>
                    @elseif($currentDue && $currentDue->pendingPayment)
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/70">
                            Menunggu Verifikasi
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200/70">
                            Belum Lunas
                        </span>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-stone-50 p-3.5 rounded-lg border border-stone-100">
                        <span class="text-xs text-stone-500 block">Nominal Iuran Pekan Ini</span>
                        <span class="text-2xl font-bold font-mono text-stone-900 mt-1 block">
                            Rp {{ number_format($currentDue?->amount ?? ($activePeriod?->amount ?? 10000), 0, ',', '.') }}
                        </span>
                        @if($currentDue && $currentDue->isPaid())
                            <span class="text-[11px] text-emerald-700 font-medium mt-0.5 block">&bull; Sudah terverifikasi</span>
                        @elseif($currentDue && $currentDue->pendingPayment)
                            <span class="text-[11px] text-amber-700 font-medium mt-0.5 block">&bull; Dalam antrean bendahara</span>
                        @else
                            <span class="text-[11px] text-rose-700 font-medium mt-0.5 block">&bull; Menunggu pembayaran</span>
                        @endif
                    </div>
                    <div class="bg-stone-50 p-3.5 rounded-lg border border-stone-100">
                        <span class="text-xs text-stone-500 block">Jatuh Tempo Pekan Ini</span>
                        <span class="text-sm font-semibold text-stone-800 mt-2 block">
                            {{ $activePeriod?->due_date ? $activePeriod->due_date->format('d M Y') : 'Jumat perkuliahan' }}
                        </span>
                        <span class="text-[11px] text-stone-400 block">Setiap Jumat perkuliahan</span>
                    </div>
                </div>

                <p class="text-xs text-stone-500 mt-4 leading-relaxed">
                    @if($currentDue && $currentDue->isPaid())
                        Pembayaran iuran kas Anda untuk {{ $activePeriod->name ?? 'pekan ini' }} telah diverifikasi oleh bendahara kelas. Tidak ada tunggakan berjalan untuk pekan ini.
                    @elseif($currentDue && $currentDue->pendingPayment)
                        Bukti transfer Anda untuk {{ $activePeriod->name ?? 'pekan ini' }} telah masuk ke sistem dan sedang menunggu persetujuan verifikasi oleh bendahara.
                    @else
                        Anda belum melunasi iuran kas untuk {{ $activePeriod->name ?? 'pekan ini' }}. Segera lakukan transfer dan kirimkan bukti pembayaran sebelum batas waktu jatuh tempo.
                    @endif
                </p>
            </div>

            <!-- Payment Action CTA -->
            <div class="mt-5 pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-center gap-3">
                <button type="button" 
                        @click="paymentModalOpen = true" 
                        class="w-full sm:w-auto flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-800 hover:bg-emerald-900 text-white font-semibold text-xs transition shadow-2xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Bayar Kas / Bayar di Muka</span>
                </button>
                <div class="text-[11px] text-stone-500 text-center sm:text-left">
                    <span>Rekening Kas: <strong class="text-stone-700 font-mono">BCA 873-019-2819</strong></span>
                </div>
            </div>
        </div>

        <!-- Focus 4: Outstanding Payments / Tunggakan & Progress (md:col-span-5) -->
        <div class="md:col-span-5 bg-white p-5 sm:p-6 rounded-xl border border-stone-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Status Kewajiban Kas</span>
                <div class="mt-2 flex items-baseline justify-between">
                    <div>
                        <h4 class="text-2xl font-bold font-mono text-stone-900">
                            Rp {{ number_format($unpaidAmount, 0, ',', '.') }}
                        </h4>
                        @if($unpaidAmount == 0)
                            <span class="text-xs font-semibold text-emerald-800">Tidak ada tunggakan</span>
                        @else
                            <span class="text-xs font-semibold text-rose-700">{{ $unpaidDues->count() }} Pekan Belum Lunas</span>
                        @endif
                    </div>
                    <span class="px-2 py-0.5 rounded text-[11px] font-medium {{ $unpaidAmount == 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-rose-50 text-rose-800 border border-rose-200/60' }}">
                        {{ $unpaidAmount == 0 ? 'Tertib' : 'Tertunggak' }}
                    </span>
                </div>

                <!-- Dues Progress Across Semester -->
                <div class="mt-6 pt-4 border-t border-stone-100 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-stone-600 font-medium">Progres Iuran Semester Ini</span>
                        <span class="font-mono font-semibold text-stone-900">
                            {{ $paidWeeksCount }} / {{ $totalWeeksCount }} Pekan ({{ $progressPercent }}%)
                        </span>
                    </div>
                    <div class="w-full h-2 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-700 rounded-full transition-all duration-500" style="width: {{ min($progressPercent, 100) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-stone-400">
                        <span>Total disetor: <strong class="text-stone-700 font-mono">Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</strong></span>
                        <span>Sisa: <strong class="text-stone-700 font-mono">{{ max(0, $totalWeeksCount - $paidWeeksCount) }} Pekan</strong></span>
                    </div>
                </div>
            </div>

            <!-- Mini Matrix Status Strip -->
            <div class="mt-5 pt-4 border-t border-stone-100">
                <span class="text-[11px] text-stone-500 font-medium block mb-2">Status Pelunasan Pekan 1 s/d {{ $totalWeeksCount }}:</span>
                <div class="grid grid-cols-8 gap-1 text-center font-mono text-[10px]">
                    @foreach($allPeriods as $period)
                        @php
                            $due = $studentDuesMap->get($period->id);
                            $isPaid = $due?->isPaid();
                            $isPending = $due?->pendingPayment !== null;
                            $isActive = $period->id === ($activePeriod?->id ?? null);
                        @endphp
                        @if($isPaid)
                            <div class="py-1 rounded bg-emerald-100 text-emerald-800 font-semibold {{ $isActive ? 'ring-2 ring-emerald-600' : '' }}" 
                                 title="{{ $period->name }}: Lunas">
                                P{{ $period->week_number }}
                            </div>
                        @elseif($isPending)
                            <div class="py-1 rounded bg-amber-100 text-amber-800 font-semibold ring-1 ring-amber-400" 
                                 title="{{ $period->name }}: Menunggu Verifikasi">
                                P{{ $period->week_number }}
                            </div>
                        @elseif($isActive)
                            <div class="py-1 rounded bg-rose-50 text-rose-700 font-semibold ring-2 ring-rose-400" 
                                 title="{{ $period->name }}: Belum Bayar (Pekan Aktif)">
                                P{{ $period->week_number }}
                            </div>
                        @else
                            <div class="py-1 rounded bg-stone-100 text-stone-400" 
                                 title="{{ $period->name }}: Belum Bayar">
                                P{{ $period->week_number }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    <!-- Lower Section: Recent Payment History (Focus 5) & Simple Class Financial Summary (Focus 6) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Focus 5: Recent Personal Payment History (lg:col-span-8) -->
        <div id="riwayat-pembayaran" class="lg:col-span-8 bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-stone-900 text-base tracking-tight">Riwayat Pembayaran Kas Saya</h3>
                    <p class="text-xs text-stone-500">Catatan setoran iuran dan status verifikasi bendahara</p>
                </div>
                <span class="text-xs font-mono text-stone-500 bg-stone-100 px-2 py-1 rounded">
                    Total: {{ $payments->count() }} Setoran
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50/80 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                        <tr>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Keterangan Iuran</th>
                            <th class="py-3 px-4">Metode Bayar</th>
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
                                        Iuran Kas {{ $payment->studentDue?->cashPeriod?->name ?? 'Kas' }}
                                    </span>
                                    <span class="text-[11px] text-stone-400">
                                        Semester Genap 2025/2026
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-stone-700 whitespace-nowrap">
                                    @if($payment->payment_method === 'cash')
                                        Tunai (Di Kelas)
                                    @elseif($payment->payment_method === 'bank_transfer')
                                        Transfer BCA
                                    @elseif($payment->payment_method === 'qris')
                                        QRIS Kas Kelas
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
                                                class="text-[11px] text-stone-600 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2 py-1 rounded transition cursor-pointer">
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

            <div class="p-3 bg-stone-50 border-t border-stone-100 text-center">
                <span class="text-xs text-stone-500">Semua riwayat setoran terhubung langsung ke pembukuan kas kelas</span>
            </div>
        </div>

        <!-- Focus 6: Simple Class Financial Summary (lg:col-span-4) -->
        <div id="keuangan-kelas" class="lg:col-span-4 space-y-5">
            
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
                    <div>
                        <h4 class="font-semibold text-stone-900 text-sm">Transparansi Kas Kelas</h4>
                        <p class="text-xs text-stone-500">Ringkasan kas kelas TI-3A</p>
                    </div>
                    <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded bg-stone-100 text-stone-600">Terbuka</span>
                </div>

                <!-- Simple Saldo Box -->
                <div class="bg-stone-50 p-3.5 rounded-lg border border-stone-100 space-y-1 mb-4">
                    <span class="text-[11px] text-stone-500 block">Total Sisa Saldo Kas Kelas</span>
                    <span class="text-2xl font-bold font-mono text-stone-900 block">
                        Rp {{ number_format($currentBalance, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-stone-400 block font-mono">BCA Kas: 873-019-2819</span>
                </div>

                <!-- In & Out summary -->
                <div class="grid grid-cols-2 gap-2 text-xs mb-4">
                    <div class="p-2.5 rounded-lg border border-stone-100 bg-stone-50/50">
                        <span class="text-stone-400 block text-[10px] uppercase">Pemasukan</span>
                        <span class="font-bold font-mono text-emerald-800 text-xs">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="p-2.5 rounded-lg border border-stone-100 bg-stone-50/50">
                        <span class="text-stone-400 block text-[10px] uppercase">Pengeluaran</span>
                        <span class="font-bold font-mono text-stone-800 text-xs">
                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Recent Spending Snippet -->
                <div>
                    <span class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider block mb-2">Pengeluaran Terakhir Kelas:</span>
                    <ul class="space-y-2 text-xs">
                        @forelse($recentExpenses as $expense)
                            <li class="flex items-center justify-between pb-1.5 border-b border-stone-50">
                                <span class="text-stone-700 truncate pr-2" title="{{ $expense->description }}">{{ $expense->description }}</span>
                                <span class="font-mono text-stone-900 font-medium shrink-0">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <li class="text-stone-400 text-xs py-1">Belum ada pengeluaran kas.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Bendahara Contact Box -->
            <div class="bg-stone-50 p-4 rounded-xl border border-stone-200/80 text-xs">
                <p class="font-semibold text-stone-900 mb-1">Ada pertanyaan soal kas?</p>
                <p class="text-stone-500 text-[11px] mb-3">Hubungi bendahara kelas untuk konfirmasi bukti atau permohonan dispensasi.</p>
                <div class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-stone-200">
                    <div>
                        <p class="font-medium text-stone-800 text-xs">{{ $bendaharaContact->name ?? 'Nadya Putri' }} (Bendahara)</p>
                        <p class="text-[10px] text-stone-500 font-mono">{{ $bendaharaContact->phone_number ?? '0821-9876-5432' }}</p>
                    </div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $bendaharaContact->phone_number ?? '6282198765432') }}?text={{ urlencode('Halo Bendahara KASMA, saya ' . $user->name . ' (NIM ' . ($user->nim ?? '-') . ') ingin konfirmasi terkait iuran kas.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="text-[10px] text-emerald-800 hover:text-emerald-950 font-semibold bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-2 py-1 rounded transition cursor-pointer">
                        WhatsApp &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
