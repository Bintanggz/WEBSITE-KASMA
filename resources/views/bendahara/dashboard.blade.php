<x-layouts.app role="bendahara" title="Dashboard Bendahara">

    <div x-data="{
        filter: 'semua',
        expenseModalOpen: false,
        cashModalOpen: false,
        broadcastModalOpen: false,
        rejectModalOpen: false,
        rejectData: { id: null, name: '' },
        settingsModalOpen: false,
        rekapModalOpen: false,
        broadcastCopied: false,
        selectedStudentId: '{{ $studentsWithUnpaidDues->first()?->id ?? '' }}',
        studentsData: {{ Js::from($studentsWithUnpaidDues) }},
        init() {
            window.addEventListener('open-reject-modal', (e) => {
                this.rejectData = e.detail;
                this.rejectModalOpen = true;
            });
        },
        copyBroadcast() {
            const text = document.getElementById('broadcastTextarea')?.value;
            if (text) {
                navigator.clipboard.writeText(text);
                this.broadcastCopied = true;
                setTimeout(() => this.broadcastCopied = false, 2500);
            }
        },
        get currentStudentDues() {
            const student = this.studentsData.find(s => s.id == this.selectedStudentId);
            return student ? student.student_dues : [];
        }
    }">

        <!-- Header & Context Banner -->
        <div class="mb-6 pb-4 border-b border-stone-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-stone-900 tracking-tight">Panel Pengelolaan Kas &bull; Bendahara</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-900 border border-amber-200/70">
                            {{ $activePeriod->name ?? 'Pekan Aktif' }} Aktif
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Kelas TI-3A &bull; Pengelola: <span class="font-medium text-stone-700">{{ $user->name }} (Bendahara 1)</span>
                    </p>
                </div>

                <!-- Quick Actions Header CTAs -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" 
                            @click="expenseModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Catat Pengeluaran</span>
                    </button>
                    <button type="button" 
                            @click="cashModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-stone-700 bg-white border border-stone-200 hover:bg-stone-50 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Catat Setoran Tunai</span>
                    </button>
                </div>
            </div>

            <!-- Pending Alert Notice -->
            @if($pendingPaymentsCount > 0)
            <div class="mt-3 p-3 rounded-lg bg-amber-50/80 border border-amber-200 text-amber-950 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Ada <strong>{{ $pendingPaymentsCount }} bukti transfer masuk</strong> (Rp {{ number_format($pendingPaymentsAmount, 0, ',', '.') }}) yang perlu dicek dan disetujui.</span>
                </div>
                <a href="#verifikasi-pembayaran" class="font-semibold underline hover:text-amber-900 shrink-0 ml-2">Tinjau Sekarang &rarr;</a>
            </div>
            @endif
        </div>

        <!-- Top Focus Metrics: 1. Current Balance & 2. Weekly Collection -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            
            <!-- Focus 1: Current Cash Balance -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Saldo Kas Kelas Saat Ini</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">Aktif</span>
                </div>
                <div class="mt-2.5">
                    <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900">
                        Rp {{ number_format($currentBalance, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>Rekening BCA Kas</span>
                    <span class="font-mono text-stone-700 font-medium">873-019-2819</span>
                </div>
            </div>

            <!-- Focus 2: Weekly Collection (Active Period) -->
            <div id="iuran-mingguan" class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Iuran {{ $activePeriod->name ?? 'Pekan Ini' }} Terkumpul</span>
                    <span class="text-[11px] font-mono text-stone-700 font-semibold bg-stone-100 px-1.5 py-0.5 rounded">
                        {{ $activePeriodPercentage }}%
                    </span>
                </div>
                <div class="mt-2.5">
                    <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900">
                        Rp {{ number_format($activePeriodCollectedAmount, 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>{{ $activePeriodPaidCount }} dari {{ $totalStudentsCount }} Mahasiswa Lunas</span>
                    <span class="text-stone-700 font-mono">Target: {{ number_format($activePeriodTargetAmount / 1000, 0) }}rb</span>
                </div>
            </div>

            <!-- Focus 3 Metric: Pending Payments -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Menunggu Verifikasi</span>
                    <span class="w-2 h-2 rounded-full {{ $pendingPaymentsCount > 0 ? 'bg-amber-500' : 'bg-stone-300' }}"></span>
                </div>
                <div class="mt-2.5 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-bold font-mono text-amber-900">{{ $pendingPaymentsCount }} Bukti</span>
                    <span class="text-xs text-stone-500 font-mono">(Rp {{ number_format($pendingPaymentsAmount, 0, ',', '.') }})</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>Setoran Transfer Siswa</span>
                    <a href="#verifikasi-pembayaran" class="text-amber-800 font-semibold hover:underline">Periksa &rarr;</a>
                </div>
            </div>

            <!-- Focus 4 Metric: Unpaid Students Count -->
            <div class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Belum Bayar Pekan Ini</span>
                    <span class="text-[10px] font-semibold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                        {{ $unpaidCount }} Mahasiswa
                    </span>
                </div>
                <div class="mt-2.5">
                    <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900">{{ $unpaidCount }} Orang</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>Tunggakan: Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</span>
                    <a href="#mahasiswa-belum-bayar" class="text-stone-700 font-semibold hover:underline">Lihat Nama &rarr;</a>
                </div>
            </div>

        </div>

        <!-- Main Grid: Left Column (Transactions & Unpaid Students) & Right Column (Verification Queue & Quick Actions) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Column: Transactions & Unpaid Students (lg:col-span-7) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Focus 4: Unpaid Students List (Daftar Mahasiswa Belum Bayar) -->
                <div id="mahasiswa-belum-bayar" class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
                    <span id="mahasiswa-kelas" class="sr-only"></span>
                    <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $unpaidCount > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                <h3 class="font-semibold text-stone-900 text-base tracking-tight">
                                    Mahasiswa Belum Bayar ({{ $activePeriod->name ?? 'Pekan Ini' }})
                                </h3>
                            </div>
                            <p class="text-xs text-stone-500 mt-0.5">
                                {{ $unpaidCount }} mahasiswa belum melunasi kas sebelum jatuh tempo {{ $activePeriod?->due_date ? $activePeriod->due_date->format('l, d M Y') : 'Jumat' }}
                            </p>
                        </div>
                        <button type="button" 
                                @click="broadcastModalOpen = true"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition self-start sm:self-auto cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Ingatkan Semua via WA</span>
                        </button>
                    </div>

                    <div class="divide-y divide-stone-100 text-xs">
                        @forelse($unpaidStudents as $index => $due)
                            @php
                                $student = $due->user;
                                $cleanPhone = preg_replace('/[^0-9]/', '', $student->phone_number ?? '6280000000000');
                                $reminderMsg = "Halo {$student->name}, mengingatkan bahwa iuran kas kelas TI-3A untuk {$activePeriod->name} sebesar Rp " . number_format($due->amount, 0, ',', '.') . " belum tercatat lunas. Mohon segera transfer ke BCA 873-019-2819 a.n Bendahara Kas TI-3A. Terima kasih!";
                            @endphp
                            <div class="p-3.5 flex items-center justify-between hover:bg-stone-50/50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-full bg-stone-100 text-stone-600 flex items-center justify-center font-mono font-semibold text-xs">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-stone-900">{{ $student->name }}</p>
                                        <p class="text-[11px] font-mono text-stone-400">
                                            NIM: {{ $student->nim ?? '-' }} &bull; Tunggakan: 1 Pekan
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-semibold text-rose-800">
                                        Rp {{ number_format($due->amount, 0, ',', '.') }}
                                    </span>
                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($reminderMsg) }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="text-[11px] px-2 py-1 rounded bg-stone-100 hover:bg-emerald-50 hover:text-emerald-800 text-stone-600 transition font-medium cursor-pointer">
                                        Ingatkan WA
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-stone-400 text-xs">
                                Luar biasa! Seluruh mahasiswa telah melunasi iuran kas untuk pekan ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Focus 5: Recent Transactions (Catatan Transaksi Kas Terkini) -->
                <div id="transaksi-kas" class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
                    <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-stone-900 text-base tracking-tight">Catatan Transaksi Terbaru</h3>
                            <p class="text-xs text-stone-500">Mutasi kas masuk dan kas keluar kelas TI-3A</p>
                        </div>
                        
                        <!-- Filter Pills -->
                        <div class="flex items-center gap-1 bg-stone-100 p-1 rounded-lg border border-stone-200/80 text-xs">
                            <button type="button" 
                                    @click="filter = 'semua'" 
                                    :class="filter === 'semua' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                Semua
                            </button>
                            <button type="button" 
                                    @click="filter = 'masuk'" 
                                    :class="filter === 'masuk' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                Pemasukan
                            </button>
                            <button type="button" 
                                    @click="filter = 'keluar'" 
                                    :class="filter === 'keluar' ? 'bg-white text-stone-900 font-semibold shadow-xs' : 'text-stone-500 hover:text-stone-800'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                Pengeluaran
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-stone-600">
                            <thead class="bg-stone-50/80 text-[11px] uppercase tracking-wider text-stone-500 font-semibold border-b border-stone-100">
                                <tr>
                                    <th class="py-3 px-4">Tanggal</th>
                                    <th class="py-3 px-4">Keterangan</th>
                                    <th class="py-3 px-4">Kategori</th>
                                    <th class="py-3 px-4 text-right">Nominal</th>
                                    <th class="py-3 px-4 text-center">Nota / Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @forelse($transactions as $trx)
                                    @php
                                        $isIncome = $trx->type === 'income';
                                        $receiptUrl = $trx->payment ? route('payments.proof', $trx->payment) : ($trx->receipt_path ? asset('storage/' . $trx->receipt_path) : null);
                                        $formattedDate = $trx->transaction_date ? \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') : $trx->created_at->format('d M Y');
                                    @endphp
                                    <tr class="hover:bg-stone-50/50 transition" 
                                        x-show="filter === 'semua' || (filter === 'masuk' && {{ $isIncome ? 'true' : 'false' }}) || (filter === 'keluar' && {{ ! $isIncome ? 'true' : 'false' }})">
                                        <td class="py-3.5 px-4 font-mono text-stone-500 whitespace-nowrap">
                                            {{ $formattedDate }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-medium text-stone-900">{{ $trx->description }}</div>
                                            <div class="text-[11px] text-stone-400">
                                                {{ $isIncome ? 'Pemasukan Kas Kelas' : 'Pengeluaran Resmi Kelas' }}
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $isIncome ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/60' : 'bg-stone-100 text-stone-700 border border-stone-200' }}">
                                                {{ ucfirst(str_replace('_', ' ', $trx->category)) }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-mono font-semibold whitespace-nowrap {{ $isIncome ? 'text-emerald-800' : 'text-rose-800' }}">
                                            {{ $isIncome ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($receiptUrl)
                                                <button type="button" 
                                                        @click="openProof({
                                                            name: '{{ addslashes($trx->description) }}',
                                                            amount: 'Rp {{ number_format($trx->amount, 0, ',', '.') }}',
                                                            method: '{{ $isIncome ? 'Kas Masuk' : 'Pengeluaran' }}',
                                                            time: '{{ $formattedDate }}',
                                                            weeks: '{{ ucfirst($trx->category) }}',
                                                            proof_url: '{{ $receiptUrl }}',
                                                            is_pending: false
                                                        })"
                                                        class="text-[11px] text-stone-500 hover:text-stone-900 bg-stone-100 hover:bg-stone-200 px-2 py-1 rounded transition cursor-pointer">
                                                    Lihat
                                                </button>
                                            @else
                                                <span class="text-stone-400 font-mono">&mdash;</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-4 text-center text-stone-400">
                                            Belum ada catatan mutasi kas kelas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-stone-50 border-t border-stone-100 text-center">
                        <span class="text-xs text-stone-500">
                            Menampilkan seluruh riwayat pembukuan kas kelas &bull; Transparan & Akuntabel
                        </span>
                    </div>
                </div>

            </div>

            <!-- Right Column: Focus 3. Pending Payments Verification Queue & Focus 6. Quick Actions (lg:col-span-5) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Focus 3: Pending Payments Verification Queue (Verifikasi Pembayaran) -->
                <div id="verifikasi-pembayaran" class="bg-white rounded-xl border border-stone-200/90 shadow-2xs p-5">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-stone-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $pendingPaymentsCount > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            <h3 class="font-semibold text-stone-900 text-base tracking-tight">Antrean Verifikasi Pembayaran</h3>
                        </div>
                        <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded {{ $pendingPaymentsCount > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200/60' : 'bg-stone-100 text-stone-600' }}">
                            {{ $pendingPaymentsCount }} Menunggu
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($pendingPayments as $payment)
                            @php
                                $student = $payment->studentDue?->user;
                                $periodName = $payment->studentDue?->cashPeriod?->name ?? 'Pekan Kas';
                                $proofUrl = $payment->proof_file_path ? route('payments.proof', $payment) : null;
                            @endphp
                            <div class="p-3.5 rounded-lg border border-stone-200 bg-stone-50/60 space-y-2.5">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-stone-900 text-xs">{{ $student->name ?? 'Mahasiswa' }}</h4>
                                        <p class="text-[11px] font-mono text-stone-500">
                                            NIM: {{ $student->nim ?? '-' }} &bull; {{ $payment->payment_method === 'qris' ? 'QRIS' : 'Transfer BCA' }}
                                        </p>
                                    </div>
                                    <span class="text-xs font-bold font-mono text-stone-900">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-stone-600 flex items-center justify-between">
                                    <span>Untuk: <strong>{{ $periodName }}</strong></span>
                                    <span class="text-stone-400">{{ $payment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="flex gap-2 pt-1">
                                    <button type="button" 
                                            @click="openProof({
                                                name: '{{ addslashes($student->name ?? 'Mahasiswa') }} ({{ $periodName }})',
                                                amount: 'Rp {{ number_format($payment->amount, 0, ',', '.') }}',
                                                method: '{{ $payment->payment_method === 'qris' ? 'QRIS Kas' : 'Transfer BCA' }}',
                                                time: '{{ $payment->created_at->format('d M Y, H:i') }}',
                                                weeks: '{{ $periodName }}',
                                                proof_url: '{{ $proofUrl }}',
                                                payment_id: {{ $payment->id }},
                                                is_pending: true
                                            })" 
                                            class="flex-1 py-1 px-2 text-[11px] font-medium rounded bg-stone-200/80 hover:bg-stone-300 text-stone-800 transition text-center cursor-pointer">
                                        Cek Bukti
                                    </button>
                                    
                                    <form action="{{ route('bendahara.payments.approve', $payment) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="py-1 px-3 text-[11px] font-semibold rounded bg-emerald-700 hover:bg-emerald-800 text-white transition cursor-pointer">
                                            Setujui
                                        </button>
                                    </form>

                                    <button type="button" 
                                            @click="rejectData = { id: {{ $payment->id }}, name: '{{ addslashes($student->name ?? 'Mahasiswa') }}' }; rejectModalOpen = true;"
                                            class="py-1 px-2 text-[11px] font-medium rounded bg-stone-100 hover:bg-rose-50 hover:text-rose-700 text-stone-600 transition cursor-pointer">
                                        Tolak
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-stone-400 text-xs">
                                <svg class="w-8 h-8 mx-auto text-stone-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tidak ada setoran yang menunggu verifikasi saat ini.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 pt-3 border-t border-stone-100 text-center">
                        <span class="text-[11px] text-stone-400">Hanya setoran yang disetujui yang menambah saldo kas kelas.</span>
                    </div>
                </div>

                <!-- Focus 6: Quick Management Panel -->
                <div id="laporan-keuangan" class="bg-white p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                    <h4 class="font-semibold text-stone-900 text-sm mb-3">Tindakan Cepat Bendahara</h4>
                    <div class="space-y-2 text-xs">
                        <button type="button" 
                                @click="window.print()" 
                                class="w-full flex items-center justify-between p-2.5 rounded-lg bg-stone-50 hover:bg-stone-100 border border-stone-200 transition text-left cursor-pointer">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-medium text-stone-800">Cetak / Ekspor Laporan Kas (PDF)</span>
                            </div>
                            <span class="text-stone-400">&rarr;</span>
                        </button>

                        <button type="button" 
                                @click="rekapModalOpen = true"
                                class="w-full flex items-center justify-between p-2.5 rounded-lg bg-stone-50 hover:bg-stone-100 border border-stone-200 transition text-left cursor-pointer">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span class="font-medium text-stone-800">Rekap Status Pelunasan {{ $totalStudentsCount }} Mahasiswa</span>
                            </div>
                            <span class="text-stone-400">&rarr;</span>
                        </button>

                        <button type="button" 
                                @click="settingsModalOpen = true"
                                class="w-full flex items-center justify-between p-2.5 rounded-lg bg-stone-50 hover:bg-stone-100 border border-stone-200 transition text-left cursor-pointer">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="font-medium text-stone-800">Atur Iuran & Rekening Kas</span>
                            </div>
                            <span class="text-stone-400">&rarr;</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>

        <!-- MODAL: Catat Pengeluaran -->
        <div x-show="expenseModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
             @keydown.escape.window="expenseModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="expenseModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-stone-800"></span>
                        <h3 class="font-semibold text-stone-900 text-sm">Catat Pengeluaran Kas Kelas</h3>
                    </div>
                    <button type="button" @click="expenseModalOpen = false" class="text-stone-400 hover:text-stone-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('bendahara.transactions.expense.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Nominal Pengeluaran (Rp)</label>
                        <input type="number" name="amount" min="1000" step="1000" placeholder="contoh: 45000" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Kategori</label>
                        <select name="category" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                            <option value="Perlengkapan">Perlengkapan Lab / Kelas</option>
                            <option value="Dana Sosial">Dana Sosial & Rekan Sakit</option>
                            <option value="Akademik">Akademik & Modul Kuliah</option>
                            <option value="Kegiatan">Kegiatan & Acara Kelas</option>
                            <option value="Lainnya">Lain-lain</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Deskripsi / Keterangan</label>
                        <input type="text" name="description" placeholder="contoh: Pembelian spidol lab dan penghapus" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Foto Bukti Nota / Kwitansi (Opsional)</label>
                        <input type="file" name="receipt_file" accept="image/*" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-1.5 text-xs text-stone-800">
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                            Simpan Pengeluaran
                        </button>
                        <button type="button" @click="expenseModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Catat Setoran Tunai -->
        <div x-show="cashModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
             @keydown.escape.window="cashModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="cashModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h3 class="font-semibold text-stone-900 text-sm">Catat Setoran Tunai Langsung di Kelas</h3>
                    </div>
                    <button type="button" @click="cashModalOpen = false" class="text-stone-400 hover:text-stone-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if($studentsWithUnpaidDues->count() > 0)
                <form method="POST" action="{{ route('bendahara.cash-payments.store') }}" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Pilih Mahasiswa</label>
                        <select x-model="selectedStudentId" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                            @foreach($studentsWithUnpaidDues as $stu)
                                <option value="{{ $stu->id }}">{{ $stu->name }} (NIM: {{ $stu->nim ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Pilih Pekan Iuran yang Dibayarkan Tunai</label>
                        <select name="student_due_id" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                            <template x-for="due in currentStudentDues" :key="due.id">
                                <option :value="due.id" x-text="due.cash_period.name + ' - Rp ' + Number(due.amount).toLocaleString('id-ID')"></option>
                            </template>
                        </select>
                    </div>

                    <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-lg text-emerald-950">
                        <p class="font-semibold text-[11px] uppercase tracking-wider text-emerald-800">Verifikasi Langsung</p>
                        <p class="text-[11px] text-emerald-800/90 pt-0.5">
                            Setoran tunai akan otomatis diverifikasi sebagai lunas dan langsung menambah saldo kas kelas.
                        </p>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-800 hover:bg-emerald-900 transition shadow-2xs cursor-pointer">
                            Simpan Setoran Tunai
                        </button>
                        <button type="button" @click="cashModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
                @else
                <div class="p-6 text-center text-stone-500 text-xs">
                    Tidak ada mahasiswa yang memiliki tunggakan kas saat ini.
                </div>
                @endif
            </div>
        </div>

        <!-- MODAL: Tolak Pembayaran -->
        <div x-show="rejectModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
             @keydown.escape.window="rejectModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200" 
                 @click.away="rejectModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        <h3 class="font-semibold text-stone-900 text-sm" x-text="'Tolak Pembayaran - ' + rejectData.name"></h3>
                    </div>
                    <button type="button" @click="rejectModalOpen = false" class="text-stone-400 hover:text-stone-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/bendahara/payments/' + rejectData.id + '/reject'" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Alasan Penolakan</label>
                        <textarea name="rejection_reason" rows="3" class="w-full bg-stone-50 border border-stone-200 rounded-lg p-3 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>Bukti transfer buram/tidak terbaca atau nominal tidak sesuai.</textarea>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-rose-700 hover:bg-rose-800 transition shadow-2xs cursor-pointer">
                            Konfirmasi Tolak
                        </button>
                        <button type="button" @click="rejectModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Broadcast WhatsApp Pengingat Kas -->
        <div x-show="broadcastModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
             @keydown.escape.window="broadcastModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200" 
                 @click.away="broadcastModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h3 class="font-semibold text-stone-900 text-sm">Pesan Siaran Pengingat Kas Kelas</h3>
                    </div>
                    <button type="button" @click="broadcastModalOpen = false" class="text-stone-400 hover:text-stone-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @php
                    $unpaidNames = $unpaidStudents->map(fn($d) => "- " . $d->user->name)->join("\n");
                    $broadcastText = "[PENGINGAT IURAN KAS KELAS TI-3A]\n\nHalo rekan-rekan, mengingatkan kembali untuk iuran kas " . ($activePeriod->name ?? 'Pekan Ini') . " (Rp 10.000 / pekan).\n\nBatas jatuh tempo: " . ($activePeriod?->due_date ? $activePeriod->due_date->format('l, d M Y') : 'Jumat') . "\nRekening Kas: BCA 873-019-2819 a.n Bendahara Kas TI-3A.\n\nDaftar rekan yang belum lunas:\n" . ($unpaidNames ?: '- Semua lunas!') . "\n\nMohon segera melunasi dan mengunggah bukti di KASMA. Terima kasih!";
                @endphp

                <div class="mt-4 space-y-3 text-xs">
                    <p class="text-stone-500">Salin pesan siaran berikut untuk dikirimkan ke grup WhatsApp kelas:</p>
                    <textarea id="broadcastTextarea" rows="8" readonly class="w-full bg-stone-50 border border-stone-200 rounded-lg p-3 text-[11px] font-mono text-stone-800 focus:outline-none resize-none leading-relaxed">{{ $broadcastText }}</textarea>

                    <div class="flex gap-2 pt-2 border-t border-stone-100">
                        <button type="button" 
                                @click="copyBroadcast()" 
                                class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-800 hover:bg-emerald-900 transition cursor-pointer">
                            <span x-text="broadcastCopied ? 'Teks Berhasil Disalin!' : 'Salin Pesan Siaran'"></span>
                        </button>
                        <button type="button" @click="broadcastModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Atur Iuran & Rekening Kas -->
        <div x-show="settingsModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
             @keydown.escape.window="settingsModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200" 
                 @click.away="settingsModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-stone-800"></span>
                        <h3 class="font-semibold text-stone-900 text-sm">Informasi Parameter Kas & Rekening</h3>
                    </div>
                    <button type="button" @click="settingsModalOpen = false" class="text-stone-400 hover:text-stone-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3.5 text-xs">
                    <div class="bg-stone-50 border border-stone-200 rounded-lg p-3 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-stone-500">Tahun Akademik:</span>
                            <span class="font-semibold text-stone-900 font-mono">{{ $activePeriod->academic_year ?? '2025/2026' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Semester:</span>
                            <span class="font-semibold text-stone-900">{{ ucfirst($activePeriod->semester ?? 'genap') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Nominal Iuran Mingguan:</span>
                            <span class="font-semibold text-emerald-800 font-mono">Rp {{ number_format($activePeriod->amount ?? 10000, 0, ',', '.') }} / pekan</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Total Durasi Semester:</span>
                            <span class="font-semibold text-stone-900">16 Pekan Perkuliahan</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200 text-emerald-950 space-y-1">
                        <p class="font-semibold text-[11px] uppercase tracking-wider text-emerald-800">Rekening Kas Aktif</p>
                        <p class="text-xs">Bank Central Asia (BCA): <strong class="font-mono text-stone-900">873-019-2819</strong></p>
                        <p class="text-[11px] text-stone-500">Atas Nama: Bendahara Kas TI-3A ({{ $user->name }})</p>
                    </div>

                    <p class="text-[11px] text-stone-400">
                        Parameter periode semester dan nominal kas diinisialisasi pada awal semester oleh bendahara kelas.
                    </p>

                    <div class="pt-2">
                        <button type="button" @click="settingsModalOpen = false" class="w-full py-2 px-3 text-xs font-medium rounded-lg text-stone-700 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Rekap Status Seluruh Siswa -->
        <div x-show="rekapModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
             @keydown.escape.window="rekapModalOpen = false">
            <div class="bg-white rounded-xl max-w-lg w-full p-6 shadow-xl border border-stone-200 max-h-[85vh] flex flex-col" 
                 @click.away="rekapModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 shrink-0">
                    <div>
                        <h3 class="font-semibold text-stone-900 text-sm">Rekap Status Pelunasan Kas Mahasiswa</h3>
                        <p class="text-[11px] text-stone-500">Kelas TI-3A &bull; Total {{ $totalStudentsCount }} Mahasiswa</p>
                    </div>
                    <button type="button" @click="rekapModalOpen = false" class="text-stone-400 hover:text-stone-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-3 overflow-y-auto divide-y divide-stone-100 text-xs flex-1">
                    @foreach($allActiveStudents as $idx => $student)
                        <div class="py-2.5 px-2 flex items-center justify-between hover:bg-stone-50">
                            <div class="flex items-center gap-2.5">
                                <span class="font-mono text-stone-400 text-[11px] w-5 text-right">{{ $idx + 1 }}.</span>
                                <div>
                                    <p class="font-medium text-stone-900">{{ $student->name }}</p>
                                    <p class="text-[10px] font-mono text-stone-400">NIM: {{ $student->nim ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[11px] font-mono font-semibold {{ $student->unpaid_dues_count == 0 ? 'text-emerald-800' : 'text-stone-700' }}">
                                    {{ $student->paid_dues_count }} / 16 Pekan
                                </span>
                                <span class="block text-[10px] {{ $student->unpaid_dues_count == 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $student->unpaid_dues_count == 0 ? 'Lunas Penuh' : $student->unpaid_dues_count . ' Sisa Pekan' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-stone-100 shrink-0">
                    <button type="button" @click="rekapModalOpen = false" class="w-full py-2 px-3 text-xs font-medium rounded-lg text-stone-700 bg-stone-100 hover:bg-stone-200 transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
