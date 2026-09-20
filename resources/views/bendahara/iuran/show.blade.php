<x-layouts.app role="bendahara" title="Detail {{ $period->name }}">

    <div x-data="{ filter: 'semua' }">
        
        <!-- Breadcrumb & Header -->
        <div class="mb-6 pb-4 border-b border-stone-200">
            <div class="flex items-center gap-2 text-xs text-stone-500 mb-2">
                <a href="{{ route('bendahara.iuran.index') }}" class="hover:text-stone-900 transition">&larr; Kembali ke Daftar Periode</a>
                <span>&bull;</span>
                <span class="text-stone-700 font-medium">{{ $period->academic_year }} (Semester {{ ucfirst($period->semester) }})</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-stone-900 tracking-tight">Status Pembayaran {{ $period->name }}</h2>
                        @if($period->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                Pekan Kas Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Jadwal: <span class="font-mono text-stone-700">{{ $period->start_date->format('d M Y') }}</span> s/d <span class="font-mono text-stone-700">{{ $period->due_date->format('d M Y') }}</span> &bull; Iuran: <span class="font-mono font-semibold text-stone-800">Rp {{ number_format($period->amount, 0, ',', '.') }}</span>/mahasiswa
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    @if(!$period->is_active)
                        <form action="{{ route('bendahara.iuran.activate', $period) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Aktifkan Pekan Ini</span>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('bendahara.iuran.deactivate', $period) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition shadow-2xs cursor-pointer">
                                <span>Nonaktifkan</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Mahasiswa Terdaftar</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                    {{ $totalStudents }} Orang
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Seluruh mahasiswa aktif kelas</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Mahasiswa Lunas</span>
                    <span class="text-[11px] font-mono font-semibold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                        {{ $percentage }}%
                    </span>
                </div>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-emerald-800 mt-2 block">
                    {{ $paidCount }} Orang
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Telah melunasi kewajiban kas</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Belum Lunas</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono {{ $unpaidCount > 0 ? 'text-rose-700' : 'text-stone-900' }} mt-2 block">
                    {{ $unpaidCount }} Orang
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Tunggakan belum disetorkan</span>
            </div>

            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Dana Iuran Terkumpul</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                    Rp {{ number_format($paidAmount, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-500 mt-1 block font-mono">
                    Target: Rp {{ number_format($targetAmount, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Student Status Table -->
        <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-stone-900 text-base tracking-tight">Daftar Status Kewajiban Mahasiswa</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Rincian status lunas atau belum lunas untuk setiap mahasiswa aktif kelas</p>
                </div>

                <!-- Filter Pills -->
                <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-lg border border-stone-200/80 text-xs">
                    <button type="button" 
                            @click="filter = 'semua'" 
                            :class="filter === 'semua' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Semua ({{ $totalStudents }})
                    </button>
                    <button type="button" 
                            @click="filter = 'lunas'" 
                            :class="filter === 'lunas' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Lunas ({{ $paidCount }})
                    </button>
                    <button type="button" 
                            @click="filter = 'belum'" 
                            :class="filter === 'belum' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                            class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                        Belum Bayar ({{ $unpaidCount }})
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-600">
                    <thead class="bg-stone-50/90 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Mahasiswa</th>
                            <th class="py-3 px-4">NIM</th>
                            <th class="py-3 px-4 text-right">Nominal Tagihan</th>
                            <th class="py-3 px-4 text-center">Status Kewajiban</th>
                            <th class="py-3 px-4 text-right">Tanggal Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($dues as $index => $due)
                            @php
                                $isPaid = $due->isPaid();
                            @endphp
                            <tr class="hover:bg-stone-50/50 transition"
                                x-show="filter === 'semua' || (filter === 'lunas' && {{ $isPaid ? 'true' : 'false' }}) || (filter === 'belum' && {{ ! $isPaid ? 'true' : 'false' }})">
                                <td class="py-3.5 px-4 font-mono text-stone-400 text-center">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-stone-900">
                                    {{ $due->user->name ?? 'Mahasiswa' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-stone-500">
                                    {{ $due->user->nim ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-stone-900 whitespace-nowrap">
                                    Rp {{ number_format($due->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($isPaid)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/60">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-stone-400 whitespace-nowrap">
                                    {{ $due->updated_at ? $due->updated_at->format('d M Y, H:i') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3.5 bg-stone-50 border-t border-stone-100 flex items-center justify-between text-xs text-stone-500">
                <span>Status kewajiban kas diperbarui saat pembayaran diverifikasi bendahara</span>
                <span class="font-mono">Total: {{ $totalStudents }} Mahasiswa</span>
            </div>
        </div>

    </div>

</x-layouts.app>
