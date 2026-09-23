<x-layouts.app role="mahasiswa" title="Kewajiban Iuran Kas Saya">

    <!-- Header & Student Context -->
    <div class="mb-6 pb-4 border-b border-stone-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold text-stone-900 tracking-tight">Kewajiban Iuran Kas Mingguan</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                    Mahasiswa: <span class="font-medium text-stone-800">{{ $user->name }}</span> &bull; NIM: <span class="font-mono text-stone-700 font-semibold">{{ $user->nim ?? '-' }}</span> &bull; Kelas: <span class="text-stone-700">TI26A3</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if($currentDue && $currentDue->isPaid())
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/80 text-xs font-medium">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Pekan Aktif Lunas</span>
                    </span>
                @elseif($currentDue)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-800 border border-rose-200/80 text-xs font-medium">
                        <svg class="w-3.5 h-3.5 text-rose-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Pekan Aktif Belum Lunas</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Week Spotlight Card -->
    @if($activePeriod && $currentDue)
        <div class="mb-8 p-5 sm:p-6 rounded-xl border border-stone-200/90 bg-white shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-stone-100 gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-stone-400">Kewajiban Pekan Berjalan</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                            Pekan Aktif
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-stone-900 tracking-tight mt-1">
                        {{ $activePeriod->name }} &bull; {{ $activePeriod->academic_year }} (Semester {{ ucfirst($activePeriod->semester) }})
                    </h3>
                </div>

                <div>
                    @if($currentDue->isPaid())
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Status: Lunas
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                            Status: Belum Lunas
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-3.5 bg-stone-50 rounded-lg border border-stone-100">
                    <span class="text-xs text-stone-500 block">Nominal Tagihan Pekan Ini</span>
                    <span class="text-xl font-bold font-mono text-stone-900 mt-1 block">
                        Rp {{ number_format($currentDue->amount, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-stone-400 mt-0.5 block">Tarif resmi kas mingguan</span>
                </div>

                <div class="p-3.5 bg-stone-50 rounded-lg border border-stone-100">
                    <span class="text-xs text-stone-500 block">Batas Waktu Jatuh Tempo</span>
                    <span class="text-base font-semibold text-stone-800 mt-1 block">
                        {{ $activePeriod->due_date->format('d M Y') }}
                    </span>
                    <span class="text-[11px] text-stone-400 mt-0.5 block">Jumat perkuliahan</span>
                </div>

                <div class="p-3.5 bg-stone-50 rounded-lg border border-stone-100 flex flex-col justify-between">
                    <span class="text-xs text-stone-500 block">Aksi Pembayaran</span>
                    <div class="mt-1">
                        @if($currentDue->isPaid())
                            <span class="text-xs text-emerald-700 font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Telah diverifikasi lunas</span>
                            </span>
                        @elseif($currentDue->pendingPayment)
                            <span class="text-xs text-amber-800 font-semibold bg-amber-50 px-2.5 py-1 rounded border border-amber-200 inline-block">
                                Menunggu Verifikasi
                            </span>
                        @else
                            <button type="button" 
                                    @click="$dispatch('open-payment-modal', { due_id: {{ $currentDue->id }} })" 
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-800 hover:bg-emerald-900 text-white font-semibold text-xs transition cursor-pointer shadow-2xs">
                                <span>{{ $currentDue->rejectedPayment ? 'Unggah Ulang Bukti &rarr;' : 'Bayar Sekarang &rarr;' }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 4 Summary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Pekan Telah Lunas</span>
            <span class="text-2xl sm:text-3xl font-bold font-mono text-emerald-800 mt-2 block">
                {{ $paidDuesCount }} / {{ $totalDuesCount }}
            </span>
            <div class="mt-2 w-full h-1.5 bg-stone-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-700 rounded-full" style="width: {{ min($completionPercentage, 100) }}%"></div>
            </div>
            <span class="text-[11px] text-stone-400 mt-1.5 block font-mono">{{ $completionPercentage }}% terselesaikan</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Pekan Belum Lunas</span>
            <span class="text-2xl sm:text-3xl font-bold font-mono {{ $unpaidDuesCount > 0 ? 'text-rose-700' : 'text-stone-900' }} mt-2 block">
                {{ $unpaidDuesCount }} Pekan
            </span>
            <span class="text-[11px] text-stone-400 mt-1 block">
                {{ $unpaidDuesCount == 0 ? 'Tertib tanpa tunggakan' : 'Perlu disetorkan ke kasir' }}
            </span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Iuran Disetor</span>
            <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-stone-400 mt-1 block font-mono">
                Dari total Rp {{ number_format($totalObligationAmount, 0, ',', '.') }}
            </span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Sisa Tagihan Tertunggak</span>
            <span class="text-2xl sm:text-3xl font-bold font-mono {{ $totalUnpaidAmount > 0 ? 'text-rose-700' : 'text-stone-900' }} mt-2 block">
                Rp {{ number_format($totalUnpaidAmount, 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-stone-400 mt-1 block">
                {{ $totalUnpaidAmount == 0 ? 'Bebas tunggakan kas' : 'Akumulasi pekan belum bayar' }}
            </span>
        </div>
    </div>

    <!-- All Obligations Table with Filter Tabs -->
    <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden mb-8" x-data="{ filter: 'semua' }">
        <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-stone-50/40">
            <div>
                <h3 class="font-bold text-stone-900 text-base tracking-tight">Daftar Seluruh Kewajiban Kas Semester</h3>
                <p class="text-xs text-stone-500 mt-0.5">Rincian status pembayaran dan aksi setor untuk setiap pekan</p>
            </div>
            
            <!-- Quick Filter Tabs -->
            <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-lg border border-stone-200/80 text-xs">
                <button type="button" 
                        @click="filter = 'semua'"
                        :class="filter === 'semua' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                        class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                    Semua ({{ $totalDuesCount }})
                </button>
                <button type="button" 
                        @click="filter = 'belum_bayar'"
                        :class="filter === 'belum_bayar' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                        class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                    Belum Lunas ({{ $unpaidDuesCount }})
                </button>
                <button type="button" 
                        @click="filter = 'lunas'"
                        :class="filter === 'lunas' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                        class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                    Lunas ({{ $paidDuesCount }})
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-600">
                <thead class="bg-stone-50/90 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                    <tr>
                        <th class="py-3 px-4">Pekan Kas</th>
                        <th class="py-3 px-4">Semester</th>
                        <th class="py-3 px-4">Jatuh Tempo</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Status Kewajiban</th>
                        <th class="py-3 px-4 text-center">Aksi / Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($studentDues as $due)
                        @php
                            $isPaid = $due->isPaid();
                            $isActive = $due->cash_period_id === ($activePeriod?->id ?? null);
                            $isPending = $due->pendingPayment !== null;
                        @endphp
                        <tr class="hover:bg-stone-50/50 transition {{ $isActive ? 'bg-emerald-50/20' : '' }}"
                            x-show="filter === 'semua' || (filter === 'belum_bayar' && '{{ $due->status }}' === 'unpaid') || (filter === 'lunas' && '{{ $due->status }}' === 'paid')">
                            <td class="py-3.5 px-4 font-semibold text-stone-900 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($isActive)
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        <span>{{ $due->cashPeriod->name ?? ('Pekan ' . $due->cashPeriod->week_number) }}</span>
                                        <span class="text-[10px] text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Aktif</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full {{ $isPaid ? 'bg-emerald-500' : 'bg-stone-300' }}"></span>
                                        <span>{{ $due->cashPeriod->name ?? ('Pekan ' . $due->cashPeriod->week_number) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-stone-600">
                                {{ $due->cashPeriod->academic_year }} &bull; Sem {{ ucfirst($due->cashPeriod->semester) }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-stone-700">
                                {{ $due->cashPeriod->due_date ? $due->cashPeriod->due_date->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">
                                Rp {{ number_format($due->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($isPaid)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                        Lunas
                                    </span>
                                @elseif($isPending)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/60">
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($due->rejectedPayment)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/60" title="{{ $due->rejectedPayment->rejection_reason }}">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/60">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($isPaid)
                                    <span class="text-emerald-700 text-xs font-semibold flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Lunas</span>
                                    </span>
                                @elseif($isPending)
                                    <span class="text-[11px] text-amber-800 font-medium">Dalam Review</span>
                                @else
                                    <button type="button" 
                                            @click="$dispatch('open-payment-modal', { due_id: {{ $due->id }} })"
                                            class="px-2.5 py-1 rounded-md bg-emerald-800 hover:bg-emerald-900 text-white font-semibold text-[11px] transition shadow-2xs cursor-pointer">
                                        Bayar &rarr;
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-stone-400">
                                Belum ada kewajiban iuran kas yang dialokasikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3.5 bg-stone-50 border-t border-stone-100 text-center text-xs text-stone-500">
            Kewajiban kas dibuat otomatis pada saat inisialisasi periode semester oleh bendahara kelas
        </div>
    </div>

</x-layouts.app>
