<x-layouts.app role="mahasiswa" title="Kewajiban Iuran Kas Saya">

    <!-- Header & Student Context -->
    <div class="mb-6 pb-4 border-b border-zinc-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Kewajiban Iuran Kas Mingguan</h2>
                <p class="text-xs text-zinc-500 mt-0.5">
                    Mahasiswa: <span class="font-medium text-zinc-800">{{ $user->name }}</span> &bull; NIM: <span class="font-mono text-zinc-700 font-medium">{{ $user->nim ?? '-' }}</span> &bull; Kelas: <span class="text-zinc-700">TI26A3</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if($currentDue && $currentDue->isPaid())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-medium">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Pekan Aktif Lunas</span>
                    </span>
                @elseif($currentDue)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-zinc-100 text-zinc-700 border border-zinc-200 text-xs font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        <span>Pekan Aktif Belum Lunas</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Week Spotlight Card -->
    @if($activePeriod && $currentDue)
        <div class="mb-6 p-5 sm:p-6 rounded-xl border border-zinc-200 bg-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-zinc-100 gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Kewajiban Pekan Berjalan</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Pekan Aktif
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 tracking-tight mt-1">
                        {{ $activePeriod->name }} &bull; {{ $activePeriod->academic_year }} (Semester {{ ucfirst($activePeriod->semester) }})
                    </h3>
                </div>

                <div>
                    @if($currentDue->isPaid())
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Status: Lunas
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                            Status: Belum Lunas
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-3.5 bg-zinc-50 rounded-lg border border-zinc-100">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Nominal Tagihan Pekan Ini</span>
                    <span class="text-xl font-bold font-mono text-zinc-900 mt-1 block">
                        Rp {{ number_format($currentDue->amount, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-0.5 block">Tarif resmi kas mingguan</span>
                </div>

                <div class="p-3.5 bg-zinc-50 rounded-lg border border-zinc-100">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Batas Waktu Jatuh Tempo</span>
                    <span class="text-base font-semibold text-zinc-800 mt-1 block">
                        {{ $activePeriod->due_date->format('d M Y') }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-0.5 block">Hari {{ $activePeriod->due_date->translatedFormat('l') }}</span>
                </div>

                <div class="p-3.5 bg-zinc-50 rounded-lg border border-zinc-100 flex flex-col justify-between">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Aksi Pembayaran</span>
                    <div class="mt-1">
                        @if($currentDue->isPaid())
                            <span class="text-xs text-emerald-700 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Telah diverifikasi lunas</span>
                            </span>
                        @elseif($currentDue->pendingPayment)
                            <span class="text-xs text-amber-800 font-semibold bg-amber-50 px-2 py-0.5 rounded border border-amber-200 inline-block">
                                Menunggu Verifikasi
                            </span>
                        @else
                            <button type="button" 
                                    @click="$dispatch('open-payment-modal', { due_id: {{ $currentDue->id }} })" 
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs transition cursor-pointer">
                                <span>{{ $currentDue->rejectedPayment ? 'Unggah Ulang Bukti' : 'Bayar Sekarang' }}</span>
                                <span aria-hidden="true">&rarr;</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Clean Summary Metrics Strip -->
    <div class="bg-white rounded-xl border border-zinc-200 p-5 mb-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 divide-y lg:divide-y-0 lg:divide-x divide-zinc-100">
            <div class="pt-3 lg:pt-0 lg:px-4 first:lg:pl-0">
                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Pekan Telah Lunas</span>
                <span class="text-2xl font-bold font-mono text-emerald-800 mt-1 block">
                    {{ $paidDuesCount }} <span class="text-sm font-normal text-zinc-400">/ {{ $totalDuesCount }}</span>
                </span>
                <div class="mt-2 w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-600 rounded-full" style="width: {{ min($completionPercentage, 100) }}%"></div>
                </div>
                <span class="text-[11px] text-zinc-400 mt-1 block font-mono">{{ $completionPercentage }}% selesai</span>
            </div>

            <div class="pt-3 lg:pt-0 lg:px-4">
                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Pekan Belum Lunas</span>
                <span class="text-2xl font-bold font-mono {{ $unpaidDuesCount > 0 ? 'text-rose-600' : 'text-zinc-900' }} mt-1 block">
                    {{ $unpaidDuesCount }} <span class="text-sm font-normal text-zinc-400">Pekan</span>
                </span>
                <span class="text-[11px] text-zinc-400 mt-1 block">
                    {{ $unpaidDuesCount == 0 ? 'Tertib tanpa tunggakan' : 'Perlu disetor ke kasir' }}
                </span>
            </div>

            <div class="pt-3 lg:pt-0 lg:px-4">
                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Disetor</span>
                <span class="text-2xl font-bold font-mono text-zinc-900 mt-1 block">
                    Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-zinc-400 mt-1 block font-mono">
                    Total: Rp {{ number_format($totalObligationAmount, 0, ',', '.') }}
                </span>
            </div>

            <div class="pt-3 lg:pt-0 lg:px-4">
                <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Sisa Tagihan</span>
                <span class="text-2xl font-bold font-mono {{ $totalUnpaidAmount > 0 ? 'text-rose-600' : 'text-zinc-900' }} mt-1 block">
                    Rp {{ number_format($totalUnpaidAmount, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-zinc-400 mt-1 block">
                    {{ $totalUnpaidAmount == 0 ? 'Bebas tunggakan kas' : 'Akumulasi belum bayar' }}
                </span>
            </div>
        </div>
    </div>

    <!-- All Obligations Table with Filter Tabs -->
    <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden mb-8" x-data="{ filter: 'semua' }">
        <div class="p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-bold text-zinc-900 text-sm tracking-tight">Daftar Seluruh Kewajiban Kas Semester</h3>
                <p class="text-xs text-zinc-500 mt-0.5">Rincian status pembayaran dan aksi setor untuk setiap pekan</p>
            </div>
            
            <!-- Quick Filter Tabs -->
            <div class="flex items-center gap-1 bg-zinc-100 p-1 rounded-lg text-xs">
                <button type="button" 
                        @click="filter = 'semua'"
                        :class="filter === 'semua' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                        class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                    Semua ({{ $totalDuesCount }})
                </button>
                <button type="button" 
                        @click="filter = 'belum_bayar'"
                        :class="filter === 'belum_bayar' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                        class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                    Belum Lunas ({{ $unpaidDuesCount }})
                </button>
                <button type="button" 
                        @click="filter = 'lunas'"
                        :class="filter === 'lunas' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                        class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                    Lunas ({{ $paidDuesCount }})
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-zinc-600">
                <thead class="bg-zinc-50 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold border-b border-zinc-100">
                    <tr>
                        <th class="py-3 px-4">Pekan Kas</th>
                        <th class="py-3 px-4">Semester</th>
                        <th class="py-3 px-4">Jatuh Tempo</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi / Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($studentDues as $due)
                        @php
                            $isPaid = $due->isPaid();
                            $isActive = $due->cash_period_id === ($activePeriod?->id ?? null);
                            $isPending = $due->pendingPayment !== null;
                        @endphp
                        <tr class="hover:bg-zinc-50/50 transition {{ $isActive ? 'bg-emerald-50/20' : '' }}"
                            x-show="filter === 'semua' || (filter === 'belum_bayar' && '{{ $due->status }}' === 'unpaid') || (filter === 'lunas' && '{{ $due->status }}' === 'paid')">
                            <td class="py-3.5 px-4 font-semibold text-zinc-900 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @if($isActive)
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        <span>{{ $due->cashPeriod->name ?? ('Pekan ' . $due->cashPeriod->week_number) }}</span>
                                        <span class="text-[10px] text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Aktif</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full {{ $isPaid ? 'bg-emerald-500' : 'bg-zinc-300' }}"></span>
                                        <span>{{ $due->cashPeriod->name ?? ('Pekan ' . $due->cashPeriod->week_number) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-zinc-500">
                                {{ $due->cashPeriod->academic_year }} &bull; Sem {{ ucfirst($due->cashPeriod->semester) }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-zinc-700">
                                {{ $due->cashPeriod->due_date ? $due->cashPeriod->due_date->format('d M Y') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-zinc-900 whitespace-nowrap">
                                Rp {{ number_format($due->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($isPaid)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Lunas
                                    </span>
                                @elseif($isPending)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Menunggu Verifikasi
                                    </span>
                                @elseif($due->rejectedPayment)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200" title="{{ $due->rejectedPayment->rejection_reason }}">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-zinc-100 text-zinc-600 border border-zinc-200">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($isPaid)
                                    <span class="text-emerald-700 text-xs font-medium flex items-center justify-center gap-1">
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
                                            class="px-2.5 py-1 rounded-md bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-[11px] transition cursor-pointer">
                                        Bayar &rarr;
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-zinc-400">
                                Belum ada kewajiban iuran kas yang dialokasikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3.5 bg-zinc-50 border-t border-zinc-100 text-center text-xs text-zinc-400">
            Kewajiban kas dibuat otomatis pada saat inisialisasi periode semester oleh bendahara kelas
        </div>
    </div>

</x-layouts.app>
