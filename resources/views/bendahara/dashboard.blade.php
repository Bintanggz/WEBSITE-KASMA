<x-layouts.app role="bendahara" title="Dashboard Bendahara">

    <div x-data="{
        filter: 'semua',
        expenseModalOpen: false,
        cashModalOpen: false,
        broadcastModalOpen: false,
        rejectModalOpen: false,
        rejectData: { id: null, name: '' },
        settingsModalOpen: false,
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
        <div class="mb-6 pb-4 border-b border-zinc-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Panel Pengelolaan Kas Kelas</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-900 border border-amber-200">
                            {{ $activePeriod ? $activePeriod->name . ' Aktif' : 'Belum Ada Pekan Aktif' }}
                        </span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        Kelas TI26A3 &bull; Bendahara: <span class="font-medium text-zinc-700">{{ $user->name }}</span>
                    </p>
                </div>

                <!-- Quick Actions Header CTAs -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" 
                            @click="expenseModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Catat Pengeluaran</span>
                    </button>
                    <button type="button" 
                            @click="cashModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-lg text-zinc-700 bg-white border border-zinc-200 hover:bg-zinc-50 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Catat Setoran Tunai</span>
                    </button>
                </div>
            </div>

            <!-- Priority Alert: Pending Payments -->
            @if($pendingPaymentsCount > 0)
            <div class="mt-4 p-3.5 rounded-lg bg-amber-50/80 border border-amber-200 text-amber-950 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span>Terdapat <strong>{{ $pendingPaymentsCount }} bukti transfer</strong> senilai <strong>Rp {{ number_format($pendingPaymentsAmount, 0, ',', '.') }}</strong> yang menunggu persetujuan.</span>
                </div>
                <a href="#verifikasi-pembayaran" class="font-semibold text-amber-900 underline hover:text-amber-950 shrink-0 self-start sm:self-auto">
                    Tinjau Antrean Sekarang &rarr;
                </a>
            </div>
            @endif
        </div>

        <!-- 4 Primary Focus Metrics in Integrated Strip -->
        <div class="bg-white rounded-xl border border-zinc-200 p-5 mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 divide-y lg:divide-y-0 lg:divide-x divide-zinc-100">
                <!-- Focus 1: Current Cash Balance -->
                <div class="pt-3 lg:pt-0 lg:px-4 first:lg:pl-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider">Saldo Kas Riil</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">Aktif</span>
                    </div>
                    <div class="mt-1.5">
                        <span class="text-2xl font-bold font-mono {{ $currentBalance >= 0 ? 'text-zinc-900' : 'text-rose-600' }}">
                            Rp {{ number_format($currentBalance, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="mt-2 text-[11px] text-zinc-500 flex items-center justify-between">
                        <span class="text-emerald-700 font-mono">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                        <span class="text-rose-600 font-mono">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Focus 2: Weekly Collection -->
                <div class="pt-3 lg:pt-0 lg:px-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider">Iuran {{ $activePeriod->name ?? 'Pekan Ini' }}</span>
                        <span class="text-[11px] font-mono text-zinc-700 font-medium bg-zinc-100 px-1.5 py-0.5 rounded">
                            {{ $activePeriodPercentage }}%
                        </span>
                    </div>
                    <div class="mt-1.5">
                        <span class="text-2xl font-bold font-mono text-zinc-900">
                            Rp {{ number_format($activePeriodCollectedAmount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="mt-2 text-[11px] text-zinc-400">
                        {{ $activePeriodPaidCount }}/{{ $totalStudentsCount }} Mahasiswa Lunas &bull; Target {{ number_format($activePeriodTargetAmount / 1000, 0) }}rb
                    </div>
                </div>

                <!-- Focus 3: Pending Payments Queue -->
                <div class="pt-3 lg:pt-0 lg:px-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider">Menunggu Verifikasi</span>
                        <span class="w-2 h-2 rounded-full {{ $pendingPaymentsCount > 0 ? 'bg-amber-500' : 'bg-zinc-300' }}"></span>
                    </div>
                    <div class="mt-1.5">
                        <span class="text-2xl font-bold font-mono {{ $pendingPaymentsCount > 0 ? 'text-amber-900' : 'text-zinc-900' }}">
                            {{ $pendingPaymentsCount }} Bukti
                        </span>
                    </div>
                    <div class="mt-2 text-[11px] text-zinc-400 flex items-center justify-between">
                        <span>Rp {{ number_format($pendingPaymentsAmount, 0, ',', '.') }}</span>
                        <a href="{{ route('bendahara.verifikasi.index') }}" class="text-zinc-600 hover:text-zinc-900 underline font-medium">Periksa &rarr;</a>
                    </div>
                </div>

                <!-- Focus 4: Unpaid Students This Week -->
                <div class="pt-3 lg:pt-0 lg:px-4">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider">Belum Bayar Pekan Ini</span>
                        <span class="text-[10px] font-medium text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                            {{ $unpaidCount }} Orang
                        </span>
                    </div>
                    <div class="mt-1.5">
                        <span class="text-2xl font-bold font-mono text-zinc-900">{{ $unpaidCount }} Mahasiswa</span>
                    </div>
                    <div class="mt-2 text-[11px] text-zinc-400 flex items-center justify-between">
                        <span>Rp {{ number_format($unpaidAmount, 0, ',', '.') }}</span>
                        <a href="#mahasiswa-belum-bayar" class="text-zinc-600 hover:text-zinc-900 underline font-medium">Lihat Nama &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Workspace Grid: Left Column (Transactions & Unpaid List) & Right Column (Verification & Quick Actions) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Column: Transactions & Unpaid Students (lg:col-span-7) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Unpaid Students List -->
                <div id="mahasiswa-belum-bayar" class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
                    <div class="p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $unpaidCount > 0 ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                <h3 class="font-bold text-zinc-900 text-sm tracking-tight">
                                    Mahasiswa Belum Bayar ({{ $activePeriod->name ?? 'Pekan Ini' }})
                                </h3>
                            </div>
                            <p class="text-xs text-zinc-500 mt-0.5">
                                {{ $unpaidCount }} mahasiswa belum melunasi kas sebelum jatuh tempo {{ $activePeriod?->due_date ? $activePeriod->due_date->translatedFormat('d M Y') : 'Jumat' }}
                            </p>
                        </div>
                        
                        @if($unpaidCount > 0)
                        <button type="button" 
                                @click="broadcastModalOpen = true"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition self-start sm:self-auto cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span>Salin Pesan WA Kelas</span>
                        </button>
                        @endif
                    </div>

                    <div class="divide-y divide-zinc-100 text-xs max-h-80 overflow-y-auto">
                        @forelse($unpaidStudents as $index => $due)
                            @php
                                $student = $due->user;
                                $cleanPhone = preg_replace('/[^0-9]/', '', $student->phone_number ?? '6280000000000');
                                $reminderMsg = "Halo {$student->name}, mengingatkan bahwa iuran kas kelas TI26A3 untuk {$activePeriod->name} sebesar Rp " . number_format($due->amount, 0, ',', '.') . " belum tercatat lunas. Mohon segera transfer ke BCA 873-019-2819 a.n Bendahara Kas TI26A3. Terima kasih!";
                            @endphp
                            <div class="p-3.5 flex items-center justify-between hover:bg-zinc-50/50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full bg-zinc-100 text-zinc-500 flex items-center justify-center font-mono font-medium text-[11px]">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-zinc-900">{{ $student->name }}</p>
                                        <p class="text-[10px] font-mono text-zinc-400">
                                            NIM: {{ $student->nim ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-mono font-medium text-rose-600">
                                        Rp {{ number_format($due->amount, 0, ',', '.') }}
                                    </span>
                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($reminderMsg) }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="text-[11px] px-2 py-0.5 rounded bg-zinc-100 hover:bg-emerald-50 hover:text-emerald-800 text-zinc-700 transition font-medium cursor-pointer">
                                        WA
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-zinc-400 text-xs">
                                <svg class="w-8 h-8 mx-auto text-emerald-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Seluruh mahasiswa telah melunasi iuran kas untuk pekan ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Transactions (Ledger Excerpt) -->
                <div id="transaksi-kas" class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
                    <div class="p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-zinc-900 text-sm tracking-tight">Catatan Transaksi Terbaru</h3>
                            <p class="text-xs text-zinc-500">Mutasi kas masuk dan kas keluar kelas TI26A3</p>
                        </div>
                        
                        <!-- Filter Pills -->
                        <div class="flex items-center gap-1 bg-zinc-100 p-1 rounded-lg text-xs">
                            <button type="button" 
                                    @click="filter = 'semua'" 
                                    :class="filter === 'semua' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                Semua
                            </button>
                            <button type="button" 
                                    @click="filter = 'masuk'" 
                                    :class="filter === 'masuk' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                Pemasukan
                            </button>
                            <button type="button" 
                                    @click="filter = 'keluar'" 
                                    :class="filter === 'keluar' ? 'bg-white text-zinc-900 font-semibold shadow-xs' : 'text-zinc-500 hover:text-zinc-800'"
                                    class="px-2.5 py-1 rounded-md transition text-xs cursor-pointer">
                                Pengeluaran
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-zinc-600">
                            <thead class="bg-zinc-50 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold border-b border-zinc-100">
                                <tr>
                                    <th class="py-3 px-4">Tanggal</th>
                                    <th class="py-3 px-4">Keterangan</th>
                                    <th class="py-3 px-4">Kategori</th>
                                    <th class="py-3 px-4 text-right">Nominal</th>
                                    <th class="py-3 px-4 text-center">Nota / Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse($transactions as $trx)
                                    @php
                                        $isIncome = $trx->type === 'income';
                                        $receiptUrl = ($trx->receipt_path || $trx->payment?->proof_file_path) ? route('transactions.receipt', $trx) : null;
                                        $formattedDate = $trx->transaction_date ? \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') : $trx->created_at->format('d M Y');
                                    @endphp
                                    <tr class="hover:bg-zinc-50/50 transition" 
                                        x-show="filter === 'semua' || (filter === 'masuk' && {{ $isIncome ? 'true' : 'false' }}) || (filter === 'keluar' && {{ ! $isIncome ? 'true' : 'false' }})">
                                        <td class="py-3 px-4 font-mono text-zinc-500 whitespace-nowrap">
                                            {{ $formattedDate }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-zinc-900">{{ $trx->description }}</div>
                                            <div class="text-[10px] text-zinc-400">
                                                {{ $isIncome ? 'Pemasukan Kas' : 'Pengeluaran Resmi' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $isIncome ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-zinc-100 text-zinc-700 border border-zinc-200' }}">
                                                {{ ucfirst(str_replace('_', ' ', $trx->category)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-semibold whitespace-nowrap {{ $isIncome ? 'text-emerald-700' : 'text-zinc-900' }}">
                                            {{ $isIncome ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
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
                                        <td colspan="5" class="py-8 px-4 text-center text-zinc-400">
                                            Belum ada catatan mutasi kas kelas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3.5 bg-zinc-50 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-zinc-400 px-5">
                        <span>Menampilkan mutasi kas kelas terbaru</span>
                        <a href="{{ route('bendahara.transaksi.index') }}" class="font-medium text-zinc-700 hover:text-zinc-950 underline">
                            Buka Buku Transaksi Lengkap &rarr;
                        </a>
                    </div>
                </div>

            </div>

            <!-- Right Column: Verification Queue & Quick Management (lg:col-span-5) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Pending Payments Verification Queue -->
                <div id="verifikasi-pembayaran" class="bg-white rounded-xl border border-zinc-200 p-5">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-zinc-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $pendingPaymentsCount > 0 ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            <h3 class="font-bold text-zinc-900 text-sm tracking-tight">Antrean Verifikasi Pembayaran</h3>
                        </div>
                        <span class="text-xs font-mono font-medium px-2 py-0.5 rounded {{ $pendingPaymentsCount > 0 ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-zinc-100 text-zinc-600' }}">
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
                            <div class="p-3.5 rounded-lg border border-zinc-200 bg-zinc-50/60 space-y-2">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold text-zinc-900 text-xs">{{ $student->name ?? 'Mahasiswa' }}</h4>
                                        <p class="text-[10px] font-mono text-zinc-400">
                                            NIM: {{ $student->nim ?? '-' }} &bull; {{ $payment->payment_method === 'qris' ? 'QRIS' : 'Transfer BCA' }}
                                        </p>
                                    </div>
                                    <span class="text-xs font-bold font-mono text-zinc-900">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-zinc-500 flex items-center justify-between">
                                    <span>Untuk: <strong class="text-zinc-700">{{ $periodName }}</strong></span>
                                    <span class="text-zinc-400">{{ $payment->created_at->diffForHumans() }}</span>
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
                                            class="flex-1 py-1 px-2 text-[11px] font-medium rounded bg-zinc-100 hover:bg-zinc-200 text-zinc-800 transition text-center cursor-pointer">
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
                                            class="py-1 px-2 text-[11px] font-medium rounded bg-zinc-100 hover:bg-rose-50 hover:text-rose-700 text-zinc-600 transition cursor-pointer">
                                        Tolak
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-zinc-400 text-xs">
                                <svg class="w-8 h-8 mx-auto text-zinc-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tidak ada setoran yang menunggu verifikasi saat ini.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 pt-3 border-t border-zinc-100 text-center">
                        <a href="{{ route('bendahara.verifikasi.index') }}" class="text-xs font-medium text-zinc-600 hover:text-zinc-900 underline">
                            Buka Halaman Verifikasi Selengkapnya &rarr;
                        </a>
                    </div>
                </div>

                <!-- Quick Management & Module Links -->
                <div id="laporan-keuangan" class="bg-white p-5 rounded-xl border border-zinc-200">
                    <h4 class="font-bold text-zinc-900 text-sm mb-3">Akses Modul Pengelolaan</h4>
                    <div class="space-y-2 text-xs">
                        <a href="{{ route('bendahara.verifikasi.index') }}" 
                           class="w-full flex items-center justify-between p-2.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 transition text-left">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded bg-amber-50 text-amber-800 border border-amber-200 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-zinc-900 block">Verifikasi Pembayaran</span>
                                    <span class="text-[10px] text-zinc-500">Persetujuan transfer &amp; QRIS</span>
                                </div>
                            </div>
                            @if($pendingPaymentsCount > 0)
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-amber-100 text-amber-900 font-mono shrink-0">
                                    {{ $pendingPaymentsCount }} Baru
                                </span>
                            @else
                                <span class="text-zinc-400">&rarr;</span>
                            @endif
                        </a>

                        <a href="{{ route('bendahara.transaksi.index') }}" 
                           class="w-full flex items-center justify-between p-2.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 transition text-left">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded bg-zinc-100 text-zinc-800 border border-zinc-200 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-zinc-900 block">Buku Transaksi Kas</span>
                                    <span class="text-[10px] text-zinc-500">Mutasi kas masuk &amp; belanja kelas</span>
                                </div>
                            </div>
                            <span class="text-zinc-400">&rarr;</span>
                        </a>

                        <a href="{{ route('bendahara.iuran.index') }}" 
                           class="w-full flex items-center justify-between p-2.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 transition text-left">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-zinc-900 block">Kelola Periode Iuran</span>
                                    <span class="text-[10px] text-zinc-500">Jadwal semester &amp; nominal pekan</span>
                                </div>
                            </div>
                            <span class="text-zinc-400">&rarr;</span>
                        </a>

                        <a href="{{ route('bendahara.laporan.index') }}" 
                           class="w-full flex items-center justify-between p-2.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 transition text-left">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded bg-zinc-100 text-zinc-800 border border-zinc-200 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-zinc-900 block">Laporan &amp; Rekapitulasi</span>
                                    <span class="text-[10px] text-zinc-500">Laporan keuangan &amp; cetak resmi</span>
                                </div>
                            </div>
                            <span class="text-zinc-400">&rarr;</span>
                        </a>

                        <div class="pt-2 border-t border-zinc-100">
                            <button type="button" 
                                    @click="settingsModalOpen = true"
                                    class="w-full py-1.5 px-3 text-center rounded-lg bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-700 text-xs font-medium transition cursor-pointer">
                                Lihat Info Rekening Kas Resmi
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- MODAL: Catat Pengeluaran -->
        <div x-show="expenseModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="expenseModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="expenseModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-zinc-800"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm">Catat Pengeluaran Kas Kelas</h3>
                    </div>
                    <button type="button" @click="expenseModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('bendahara.transactions.expense.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Nominal Pengeluaran (Rp)</label>
                        <input type="number" name="amount" min="1000" step="1000" placeholder="contoh: 45000" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Kategori</label>
                        <select name="category" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                            <option value="Perlengkapan">Perlengkapan Lab / Kelas</option>
                            <option value="Dana Sosial">Dana Sosial & Rekan Sakit</option>
                            <option value="Akademik">Akademik & Modul Kuliah</option>
                            <option value="Kegiatan">Kegiatan & Acara Kelas</option>
                            <option value="Lainnya">Lain-lain</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Tanggal Transaksi</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Deskripsi / Keterangan</label>
                        <input type="text" name="description" placeholder="contoh: Pembelian spidol lab dan penghapus" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Foto Bukti Nota / Kwitansi (Opsional)</label>
                        <input type="file" name="receipt_file" accept="image/*" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-1.5 text-xs text-zinc-800">
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                            Simpan Pengeluaran
                        </button>
                        <button type="button" @click="expenseModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Catat Setoran Tunai -->
        <div x-show="cashModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="cashModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="cashModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm">Catat Setoran Tunai di Kelas</h3>
                    </div>
                    <button type="button" @click="cashModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if($studentsWithUnpaidDues->count() > 0)
                <form method="POST" action="{{ route('bendahara.cash-payments.store') }}" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Pilih Mahasiswa</label>
                        <select x-model="selectedStudentId" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                            @foreach($studentsWithUnpaidDues as $stu)
                                <option value="{{ $stu->id }}">{{ $stu->name }} (NIM: {{ $stu->nim ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Pilih Pekan Iuran yang Dibayarkan Tunai</label>
                        <select name="student_due_id" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                            <template x-for="due in currentStudentDues" :key="due.id">
                                <option :value="due.id" x-text="due.cash_period.name + ' - Rp ' + Number(due.amount).toLocaleString('id-ID')"></option>
                            </template>
                        </select>
                    </div>

                    <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-700">
                        <p class="font-semibold text-[11px] uppercase tracking-wider text-zinc-500">Verifikasi Langsung</p>
                        <p class="text-[11px] text-zinc-600 pt-0.5">
                            Setoran tunai otomatis berstatus lunas dan langsung menambah saldo kas kelas.
                        </p>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 transition cursor-pointer">
                            Simpan Setoran Tunai
                        </button>
                        <button type="button" @click="cashModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
                @else
                <div class="p-6 text-center text-zinc-500 text-xs">
                    Tidak ada mahasiswa yang memiliki tunggakan kas saat ini.
                </div>
                @endif
            </div>
        </div>

        <!-- MODAL: Tolak Pembayaran -->
        <div x-show="rejectModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="rejectModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200" 
                 @click.away="rejectModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm" x-text="'Tolak Pembayaran - ' + rejectData.name"></h3>
                    </div>
                    <button type="button" @click="rejectModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/bendahara/payments/' + rejectData.id + '/reject'" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Alasan Penolakan</label>
                        <textarea name="rejection_reason" rows="3" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg p-3 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>Bukti transfer buram/tidak terbaca atau nominal tidak sesuai.</textarea>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-rose-700 hover:bg-rose-800 transition cursor-pointer">
                            Konfirmasi Tolak
                        </button>
                        <button type="button" @click="rejectModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Broadcast WhatsApp Pengingat Kas -->
        <div x-show="broadcastModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="broadcastModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200" 
                 @click.away="broadcastModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm">Pesan Siaran Pengingat Kas Kelas</h3>
                    </div>
                    <button type="button" @click="broadcastModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @php
                    $unpaidNames = $unpaidStudents->map(fn($d) => "- " . $d->user->name)->join("\n");
                    $broadcastText = "[PENGINGAT IURAN KAS KELAS TI26A3]\n\nHalo rekan-rekan, mengingatkan kembali untuk iuran kas " . ($activePeriod->name ?? 'Pekan Ini') . " (Rp 10.000 / pekan).\n\nBatas jatuh tempo: " . ($activePeriod?->due_date ? $activePeriod->due_date->translatedFormat('l, d M Y') : 'Jumat') . "\nRekening Kas: BCA 873-019-2819 a.n Bendahara Kas TI26A3.\n\nDaftar rekan yang belum lunas:\n" . ($unpaidNames ?: '- Semua lunas!') . "\n\nMohon segera melunasi dan mengunggah bukti di KASMA. Terima kasih!";
                @endphp

                <div class="mt-4 space-y-3 text-xs">
                    <p class="text-zinc-500">Salin teks siaran berikut untuk dikirim ke grup WhatsApp kelas:</p>
                    <textarea id="broadcastTextarea" rows="8" readonly class="w-full bg-zinc-50 border border-zinc-200 rounded-lg p-3 text-[11px] font-mono text-zinc-800 focus:outline-none resize-none leading-relaxed">{{ $broadcastText }}</textarea>

                    <div class="flex gap-2 pt-2 border-t border-zinc-100">
                        <button type="button" 
                                @click="copyBroadcast()" 
                                class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 transition cursor-pointer">
                            <span x-text="broadcastCopied ? 'Teks Berhasil Disalin!' : 'Salin Pesan Siaran'"></span>
                        </button>
                        <button type="button" @click="broadcastModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Atur Iuran & Rekening Kas -->
        <div x-show="settingsModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="settingsModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200" 
                 @click.away="settingsModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-zinc-800"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm">Informasi Parameter Kas &amp; Rekening</h3>
                    </div>
                    <button type="button" @click="settingsModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3 text-xs">
                    <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-3 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Tahun Akademik:</span>
                            <span class="font-semibold text-zinc-900 font-mono">{{ $activePeriod->academic_year ?? '2025/2026' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Semester:</span>
                            <span class="font-semibold text-zinc-900">{{ ucfirst($activePeriod->semester ?? 'genap') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Nominal Iuran Mingguan:</span>
                            <span class="font-semibold text-emerald-700 font-mono">Rp {{ number_format($activePeriod->amount ?? 10000, 0, ',', '.') }} / pekan</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Total Durasi Semester:</span>
                            <span class="font-semibold text-zinc-900">16 Pekan Perkuliahan</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200 text-zinc-800 space-y-1">
                        <p class="font-semibold text-[11px] uppercase tracking-wider text-zinc-500">Rekening Kas Aktif</p>
                        <p class="text-xs">Bank Central Asia (BCA): <strong class="font-mono text-zinc-900">873-019-2819</strong></p>
                        <p class="text-[11px] text-zinc-500">Atas Nama: Bendahara Kas TI26A3 ({{ $user->name }})</p>
                    </div>

                    <p class="text-[11px] text-zinc-400">
                        Parameter periode semester dan nominal kas diatur pada modul Iuran Kas oleh bendahara kelas.
                    </p>

                    <div class="pt-2">
                        <button type="button" @click="settingsModalOpen = false" class="w-full py-2 px-3 text-xs font-medium rounded-lg text-zinc-700 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
