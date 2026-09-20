<x-layouts.app role="bendahara" title="Kelola Iuran Kas & Periode">

    <div x-data="{
        initModalOpen: false,
        activeAcademicYear: '{{ date('Y') . '/' . (date('Y') + 1) }}',
        activeSemester: 'genap',
        activeWeeks: 16,
        activeAmount: 10000,
        startDate: '{{ date('Y-m-d') }}',
        dueDate: '{{ date('Y-m-d', strtotime('+7 days')) }}'
    }">

        <!-- Header Banner -->
        <div class="mb-6 pb-4 border-b border-stone-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-stone-900 tracking-tight">Kelola Iuran Kas & Periode Mingguan</h2>
                        @if($activePeriod)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                {{ $activePeriod->name }} Sedang Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                Belum Ada Pekan Aktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Konfigurasi jadwal periode kas semester, kelola pekan aktif, dan pantau status kewajiban mahasiswa.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="initModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Inisialisasi Periode Semester</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Periode Terdaftar</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                    {{ $periods->count() }} Pekan
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Tercatat dalam sistem</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Pekan Kas Berjalan</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-emerald-800 mt-2 block">
                    {{ $activePeriod ? $activePeriod->name : 'Nonaktif' }}
                </span>
                <span class="text-[11px] text-stone-500 mt-1 block">
                    {{ $activePeriod ? ('Jatuh tempo: ' . $activePeriod->due_date->format('d M Y')) : 'Pilih pekan untuk diaktifkan' }}
                </span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Mahasiswa Aktif Kelas</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                    {{ $totalStudents }} Orang
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Menerima alokasi kewajiban iuran</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Rata-rata Pelunasan Total</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                    {{ $overallPercentage }}%
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Akumulasi seluruh pekan kas</span>
            </div>
        </div>

        <!-- Grouped Periods List -->
        @forelse($groupedPeriods as $groupTitle => $groupList)
            <div class="mb-8 bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
                <div class="p-5 border-b border-stone-100 flex items-center justify-between bg-stone-50/60">
                    <div>
                        <h3 class="font-bold text-stone-900 text-base tracking-tight">{{ $groupTitle }}</h3>
                        <p class="text-xs text-stone-500 mt-0.5">Daftar jadwal iuran kas mingguan dan rekap pembayaran mahasiswa</p>
                    </div>
                    <span class="text-xs font-mono text-stone-600 bg-white border border-stone-200 px-2.5 py-1 rounded-md">
                        {{ $groupList->count() }} Pekan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-600">
                        <thead class="bg-stone-50/90 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                            <tr>
                                <th class="py-3 px-4">Pekan</th>
                                <th class="py-3 px-4">Jadwal Tanggal</th>
                                <th class="py-3 px-4 text-right">Nominal</th>
                                <th class="py-3 px-4 text-center">Status Pekan</th>
                                <th class="py-3 px-4">Progres Pembayaran Siswa</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($groupList as $p)
                                @php
                                    $pct = $p->total_dues_count > 0 ? round(($p->paid_dues_count / $p->total_dues_count) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="py-3.5 px-4 font-semibold text-stone-900 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            @if($p->is_active)
                                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-stone-300"></span>
                                            @endif
                                            <span>{{ $p->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap text-stone-700">
                                        <span class="font-mono">{{ $p->start_date->format('d M') }}</span>
                                        <span class="text-stone-400">&mdash;</span>
                                        <span class="font-mono font-medium text-stone-900">{{ $p->due_date->format('d M Y') }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">
                                        Rp {{ number_format($p->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($p->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                                Aktif Berjalan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-stone-100 text-stone-500 border border-stone-200">
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 min-w-[200px]">
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-[11px]">
                                                <span>{{ $p->paid_dues_count }} / {{ $p->total_dues_count }} Lunas</span>
                                                <span class="font-mono font-semibold text-stone-900">{{ $pct }}%</span>
                                            </div>
                                            <div class="w-full h-1.5 bg-stone-100 rounded-full overflow-hidden">
                                                <div class="h-full {{ $pct >= 100 ? 'bg-emerald-600' : ($pct >= 50 ? 'bg-emerald-700' : 'bg-amber-600') }} rounded-full" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('bendahara.iuran.show', $p) }}" 
                                               class="text-[11px] font-medium px-2.5 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 transition cursor-pointer">
                                                Detail Siswa &rarr;
                                            </a>

                                            @if(!$p->is_active)
                                                <form action="{{ route('bendahara.iuran.activate', $p) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="text-[11px] font-medium px-2 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition cursor-pointer"
                                                            title="Jadikan pekan aktif berjalan">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('bendahara.iuran.deactivate', $p) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="text-[11px] font-medium px-2 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-600 transition cursor-pointer"
                                                            title="Nonaktifkan status aktif">
                                                        Nonaktifkan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="p-12 text-center bg-white rounded-xl border border-stone-200/90 shadow-2xs">
                <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h4 class="font-bold text-stone-900 text-base">Belum Ada Periode Kas</h4>
                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                    Inisialisasi jadwal pekan kas untuk satu semester baru untuk mulai menagih dan melacak setoran mahasiswa.
                </p>
                <button type="button" 
                        @click="initModalOpen = true"
                        class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition cursor-pointer">
                    <span>Inisialisasi Periode Sekarang</span>
                </button>
            </div>
        @endforelse

        <!-- MODAL: Inisialisasi Periode Semester Baru -->
        <div x-show="initModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40 backdrop-blur-xs"
             @keydown.escape.window="initModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="initModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-stone-800"></span>
                        <h3 class="font-semibold text-stone-900 text-sm">Inisialisasi Periode Kas Semester</h3>
                    </div>
                    <button type="button" @click="initModalOpen = false" class="text-stone-400 hover:text-stone-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('bendahara.iuran.store') }}" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Tahun Akademik</label>
                            <input type="text" name="academic_year" value="2025/2026" placeholder="2025/2026" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400 font-mono" required>
                            <span class="text-[10px] text-stone-400">Format: YYYY/YYYY</span>
                        </div>

                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Semester</label>
                            <select name="semester" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                                <option value="genap">Genap</option>
                                <option value="ganjil">Ganjil</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Jumlah Pekan</label>
                            <input type="number" name="number_of_weeks" value="16" min="1" max="24" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400 font-mono" required>
                            <span class="text-[10px] text-stone-400">Default: 16 Pekan</span>
                        </div>

                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Iuran per Pekan (Rp)</label>
                            <input type="number" name="amount" value="10000" min="1000" step="500" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400 font-mono" required>
                            <span class="text-[10px] text-stone-400">Default: Rp 10.000</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Tanggal Mulai Pekan 1</label>
                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                        </div>

                        <div>
                            <label class="block text-stone-700 font-medium mb-1">Jatuh Tempo Pekan 1</label>
                            <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                        </div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-lg text-stone-600 space-y-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="set_first_active" value="1" checked class="rounded border-stone-300 text-stone-800 focus:ring-stone-600">
                            <span class="font-medium text-stone-900 text-[11px]">Jadikan Pekan 1 langsung aktif berjalan</span>
                        </label>
                        <p class="text-[10px] text-stone-500 pt-0.5">
                            Sistem akan otomatis membuat kewajiban kas untuk {{ $totalStudents }} mahasiswa aktif kelas.
                        </p>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                            Inisialisasi Periode
                        </button>
                        <button type="button" @click="initModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layouts.app>
