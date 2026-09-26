@props([
    'role' => 'mahasiswa',
    'title' => 'KASMA',
    'subtitle' => null,
])
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KASMA - Sistem Manajemen Uang Kas Mahasiswa yang transparan, rapi, dan mudah digunakan.">
    <title>{{ $title }} &mdash; KASMA</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-udb.png') }}">
    
    <!-- Google Font: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-zinc-900 bg-[#fafaf9] selection:bg-emerald-100 selection:text-emerald-900"
      x-data="{ 
          mobileMenuOpen: false, 
          proofModalOpen: false,
          paymentModalOpen: false,
          proofModalData: null,
          openProof(data) {
              this.proofModalData = data;
              this.proofModalOpen = true;
          }
      }"
      @open-proof-modal.window="openProof($event.detail)"
      @open-payment-modal.window="paymentModalOpen = true; if ($event.detail?.due_id) { $nextTick(() => { window.dispatchEvent(new CustomEvent('select-due-id', { detail: { id: $event.detail.due_id } })); }); }">

    <!-- Mobile Slide-out Drawer & Overlay -->
    <x-mobile-nav :role="$role" />

    <div class="min-h-full flex">
        <!-- Desktop Sidebar -->
        <x-sidebar :role="$role" />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 md:pl-64">
            <!-- Top Navigation Bar -->
            <x-topbar :role="$role" :title="$title" :subtitle="$subtitle" />

            <!-- Page Content -->
            <main class="flex-1 pb-24 md:pb-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                    
                    <!-- Flash Notification Banners -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-emerald-50/80 border border-emerald-200/80 text-emerald-950 flex items-center justify-between text-xs sm:text-sm">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                            <button type="button" @click="show = false" class="text-emerald-700 hover:text-emerald-950 text-base font-bold ml-2 leading-none cursor-pointer" aria-label="Tutup">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-rose-50/80 border border-rose-200/80 text-rose-950 flex items-center justify-between text-xs sm:text-sm">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-rose-600 shrink-0"></span>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                            <button type="button" @click="show = false" class="text-rose-700 hover:text-rose-950 text-base font-bold ml-2 leading-none cursor-pointer" aria-label="Tutup">&times;</button>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-rose-50/80 border border-rose-200/80 text-rose-950 text-xs sm:text-sm">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <span class="w-2 h-2 rounded-full bg-rose-600 shrink-0 mt-1.5"></span>
                                    <div>
                                        <span class="font-semibold block mb-1">Periksa kembali isian formulir:</span>
                                        <ul class="list-disc list-inside space-y-0.5 text-rose-900 text-xs">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button type="button" @click="show = false" class="text-rose-700 hover:text-rose-950 text-base font-bold ml-2 leading-none cursor-pointer" aria-label="Tutup">&times;</button>
                            </div>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    <!-- Proof Preview Modal -->
    <div x-show="proofModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
         @keydown.escape.window="proofModalOpen = false">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
             @click.away="proofModalOpen = false">
            <div class="flex items-center justify-between pb-3.5 border-b border-zinc-100">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <h3 class="font-semibold text-zinc-900 text-sm" x-text="'Bukti Transaksi - ' + (proofModalData?.name || '')"></h3>
                </div>
                <button type="button" @click="proofModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-4 space-y-3.5">
                <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-3 text-xs space-y-1.5 text-zinc-600">
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Nominal:</span>
                        <span class="font-semibold text-zinc-900 font-mono" x-text="proofModalData?.amount"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Metode:</span>
                        <span class="font-medium text-zinc-800" x-text="proofModalData?.method"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Waktu:</span>
                        <span class="text-zinc-700 font-mono" x-text="proofModalData?.time"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500">Keterangan:</span>
                        <span class="font-medium text-emerald-800" x-text="proofModalData?.weeks"></span>
                    </div>
                </div>
                
                <template x-if="proofModalData?.proof_url">
                    <div class="space-y-2">
                        <div class="w-full max-h-72 overflow-hidden rounded-lg border border-zinc-200 bg-zinc-100 flex items-center justify-center p-1">
                            <img :src="proofModalData.proof_url" :alt="proofModalData?.name" class="w-full max-h-72 object-contain rounded" />
                        </div>
                        <div class="flex justify-end">
                            <a :href="proofModalData.proof_url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs text-zinc-600 hover:text-zinc-900 font-medium py-1 px-2.5 rounded-lg bg-zinc-50 border border-zinc-200 hover:bg-zinc-100 transition">
                                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <span>Buka Gambar Ukuran Penuh &rarr;</span>
                            </a>
                        </div>
                    </div>
                </template>

                <template x-if="!proofModalData?.proof_url">
                    <div class="w-full h-28 bg-zinc-50 border border-zinc-200 rounded-lg flex flex-col items-center justify-center p-4 text-center">
                        <svg class="w-7 h-7 text-zinc-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-xs text-zinc-700 font-medium">Transaksi Tunai / Tanpa Berkas Digital</p>
                        <p class="text-[11px] text-zinc-400 mt-0.5">Tercatat resmi dalam buku kas kelas</p>
                    </div>
                </template>

                @if($role === 'bendahara')
                <template x-if="proofModalData?.is_pending && proofModalData?.payment_id">
                    <div class="flex gap-2 pt-2">
                        <form :action="'/bendahara/payments/' + proofModalData.payment_id + '/approve'" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 transition text-center cursor-pointer">
                                Setujui Pembayaran
                            </button>
                        </form>
                        <button type="button" 
                                @click="proofModalOpen = false; window.dispatchEvent(new CustomEvent('open-reject-modal', { detail: { id: proofModalData.payment_id, name: proofModalData.name } }))" 
                                class="py-2 px-3 text-xs font-medium rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 transition cursor-pointer">
                            Tolak
                        </button>
                    </div>
                </template>
                @endif

                <div class="pt-1">
                    <button type="button" @click="proofModalOpen = false" class="w-full py-2 px-3 text-xs font-medium rounded-lg text-zinc-700 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Payment Modal -->
    @if($role === 'mahasiswa' && auth()->check())
    @php
        $modalUnpaidDues = auth()->user()->studentDues()
            ->where('status', 'unpaid')
            ->with(['cashPeriod', 'pendingPayment', 'rejectedPayment'])
            ->get()
            ->sortBy(fn($d) => $d->cashPeriod->week_number ?? 0);

        $duesJson = $modalUnpaidDues->map(fn($d) => [
            'id' => (int)$d->id,
            'amount' => (float)$d->amount,
            'week_number' => $d->cashPeriod->week_number ?? 0,
            'name' => $d->cashPeriod->name ?? ('Pekan ' . $d->cashPeriod->week_number),
            'is_pending' => $d->pendingPayment !== null,
            'is_rejected' => $d->rejectedPayment !== null,
            'rejection_reason' => $d->rejectedPayment?->rejection_reason,
        ])->values();
    @endphp
    <div x-show="paymentModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
         @keydown.escape.window="paymentModalOpen = false"
         x-data="{
             copied: false,
             fileName: '',
             submitting: false,
             paymentMethod: 'bank_transfer',
             dues: {{ Js::from($duesJson) }},
             selectedDueIds: [],
             init() {
                 window.addEventListener('select-due-id', (e) => {
                     const id = Number(e.detail.id);
                     if (id && !this.selectedDueIds.includes(id)) {
                         this.selectedDueIds = [id];
                     }
                 });
             },
             copyAccount() {
                 navigator.clipboard.writeText('8730192819');
                 this.copied = true;
                 setTimeout(() => this.copied = false, 2500);
             },
             get totalAmount() {
                 return this.selectedDueIds.reduce((sum, id) => {
                     const due = this.dues.find(d => d.id === Number(id));
                     return sum + (due ? due.amount : 0);
                 }, 0);
             },
             formatRupiah(num) {
                 return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
             },
             selectAllAvailable() {
                 const available = this.dues.filter(d => !d.is_pending).map(d => d.id);
                 if (this.selectedDueIds.length === available.length) {
                     this.selectedDueIds = [];
                 } else {
                     this.selectedDueIds = available;
                 }
             }
         }">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
             @click.away="paymentModalOpen = false">
            <div class="flex items-center justify-between pb-3.5 border-b border-zinc-100">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <h3 class="font-semibold text-zinc-900 text-sm">Setor Kas Kelas TI26A3</h3>
                </div>
                <button type="button" @click="paymentModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mt-4 space-y-4 text-xs">
                <!-- Pilihan Metode Pembayaran Tab -->
                <div>
                    <label class="block text-zinc-700 font-semibold mb-1.5 uppercase text-[11px] tracking-wider">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" 
                                @click="paymentMethod = 'bank_transfer'" 
                                :class="paymentMethod === 'bank_transfer' ? 'border-emerald-600 bg-emerald-50/70 text-emerald-950 font-semibold' : 'border-zinc-200 bg-zinc-50/60 text-zinc-600 hover:bg-zinc-100'"
                                class="p-2.5 rounded-lg border text-left text-xs transition cursor-pointer flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="paymentMethod === 'bank_transfer' ? 'bg-emerald-600' : 'bg-zinc-300'"></span>
                            <span>Transfer BCA</span>
                        </button>
                        <button type="button" 
                                @click="paymentMethod = 'qris'" 
                                :class="paymentMethod === 'qris' ? 'border-emerald-600 bg-emerald-50/70 text-emerald-950 font-semibold' : 'border-zinc-200 bg-zinc-50/60 text-zinc-600 hover:bg-zinc-100'"
                                class="p-2.5 rounded-lg border text-left text-xs transition cursor-pointer flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="paymentMethod === 'qris' ? 'bg-emerald-600' : 'bg-zinc-300'"></span>
                            <span>QRIS Kas Kelas</span>
                        </button>
                    </div>
                </div>

                <!-- Info Rekening BCA -->
                <div x-show="paymentMethod === 'bank_transfer'" class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-800 space-y-2">
                    <p class="font-semibold text-[11px] uppercase tracking-wider text-zinc-500">Transfer Rekening Resmi Kas</p>
                    <div class="flex justify-between items-center bg-white p-2.5 rounded-md border border-zinc-200">
                        <div>
                            <span class="text-[10px] text-zinc-400 block">Bank Central Asia (BCA)</span>
                            <span class="font-mono font-bold text-zinc-900 text-xs">873-019-2819</span>
                            <span class="text-[10px] text-zinc-500 block">a.n. Bendahara Kas TI26A3</span>
                        </div>
                        <button type="button" @click="copyAccount()" class="text-[10px] bg-zinc-100 hover:bg-zinc-200 text-zinc-700 px-2 py-1 rounded font-medium border border-zinc-200 transition cursor-pointer">
                            <span x-text="copied ? 'Tersalin!' : 'Salin Rekening'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-zinc-500">Iuran: <strong class="text-zinc-800">Rp 10.000 / pekan</strong> &bull; Bisa memilih beberapa pekan sekaligus di bawah.</p>
                </div>

                <!-- Visual QRIS Kas Kelas -->
                <div x-show="paymentMethod === 'qris'" class="p-3.5 bg-zinc-900 text-white rounded-lg space-y-2.5">
                    <div class="flex items-center justify-between pb-2 border-b border-zinc-800">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold tracking-widest uppercase bg-rose-600 text-white px-1.5 py-0.5 rounded font-mono">QRIS</span>
                            <span class="text-xs font-semibold text-zinc-100">KAS KELAS TI26A3 (UDB)</span>
                        </div>
                        <span class="text-[10px] text-emerald-400 font-medium font-mono">ID1026002910</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="bg-white p-2 rounded-lg shrink-0 border border-zinc-200 text-center">
                            <svg class="w-24 h-24 mx-auto" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                            <span class="text-[9px] text-zinc-500 font-semibold uppercase tracking-wider block mt-0.5">Pindai QR</span>
                        </div>
                        <div class="space-y-1 text-[11px] text-zinc-300">
                            <p class="font-semibold text-zinc-100">Dukungan Semua Pembayaran:</p>
                            <p class="text-zinc-400 text-[10px] leading-snug">GoPay, OVO, Dana, ShopeePay, LinkAja, BCA Mobile, Livin, BRImo, dll.</p>
                            <p class="text-[10px] text-emerald-400 pt-0.5 font-medium">&bull; Masukkan nominal sesuai total pekan yang dipilih.</p>
                        </div>
                    </div>
                </div>

                @if($modalUnpaidDues->count() > 0)
                <form method="POST" action="{{ route('mahasiswa.payments.store') }}" enctype="multipart/form-data" class="space-y-4" @submit="submitting = true">
                    @csrf
                    <input type="hidden" name="payment_method" :value="paymentMethod">
                    
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-zinc-700 font-semibold uppercase text-[11px] tracking-wider">Pilih Pekan Iuran yang Dibayar</label>
                            <button type="button" @click="selectAllAvailable()" class="text-[11px] text-emerald-700 hover:text-emerald-900 font-semibold cursor-pointer underline">
                                <span x-text="selectedDueIds.length === dues.filter(d => !d.is_pending).length ? 'Batal Semua' : 'Pilih Semua'"></span>
                            </button>
                        </div>
                        <div class="border border-zinc-200 rounded-lg p-2 max-h-40 overflow-y-auto space-y-1 bg-zinc-50/60">
                            @foreach($modalUnpaidDues as $due)
                                @php 
                                    $isPending = $due->pendingPayment !== null; 
                                    $isRejected = $due->rejectedPayment !== null;
                                @endphp
                                <label class="flex items-center justify-between p-2 rounded-md hover:bg-white transition {{ $isPending ? 'opacity-50 cursor-not-allowed bg-zinc-100/50' : 'cursor-pointer' }}">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" 
                                               name="student_due_ids[]" 
                                               value="{{ $due->id }}" 
                                               x-model="selectedDueIds"
                                               {{ $isPending ? 'disabled' : '' }}
                                               class="rounded border-zinc-300 text-emerald-700 focus:ring-emerald-600">
                                        <div>
                                            <span class="text-zinc-900 font-medium block">{{ $due->cashPeriod->name ?? ('Pekan ' . $due->cashPeriod->week_number) }}</span>
                                            @if($isRejected)
                                                <span class="text-[10px] text-rose-700 font-medium block">Pernah ditolak (Unggah ulang)</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($isPending)
                                            <span class="text-[10px] text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Menunggu Verifikasi</span>
                                        @else
                                            <span class="font-mono text-zinc-700 font-semibold">Rp {{ number_format($due->amount, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- Live Total Display -->
                        <div class="mt-2.5 p-3 rounded-lg bg-zinc-900 text-white flex items-center justify-between" x-show="selectedDueIds.length > 0">
                            <div>
                                <span class="text-[10px] text-zinc-400 block uppercase tracking-wider font-semibold">Total Transfer</span>
                                <span class="font-mono text-sm sm:text-base font-bold text-emerald-400" x-text="formatRupiah(totalAmount)"></span>
                            </div>
                            <span class="text-[11px] text-zinc-300 bg-zinc-800 px-2 py-1 rounded font-medium font-mono" x-text="selectedDueIds.length + ' Pekan Terpilih'"></span>
                        </div>
                        <div class="mt-2 p-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-[11px]" x-show="selectedDueIds.length === 0">
                            <span class="font-semibold">&bull; Perhatian:</span> Silakan centang minimal satu pekan iuran kas di atas.
                        </div>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-semibold mb-1 uppercase text-[11px] tracking-wider">Unggah Bukti Transfer / Resi</label>
                        <label class="border-2 border-dashed border-zinc-200 rounded-lg p-4 text-center hover:border-zinc-300 transition cursor-pointer block bg-zinc-50/40">
                            <input type="file" name="proof_file" accept="image/*" class="hidden" required @change="fileName = $event.target.files[0]?.name">
                            <svg class="w-6 h-6 mx-auto text-zinc-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-[11px] text-zinc-700 font-medium" x-text="fileName ? fileName : 'Pilih gambar bukti transfer (JPG / PNG / WEBP)'"></p>
                            <p class="text-[10px] text-zinc-400 mt-0.5" x-show="!fileName">Format JPG, PNG, atau WEBP &bull; Maksimal 5MB</p>
                        </label>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-zinc-100">
                        <button type="submit" 
                                :disabled="submitting || selectedDueIds.length === 0"
                                class="flex-1 py-2.5 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-700 hover:bg-emerald-800 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center gap-2 cursor-pointer">
                            <template x-if="submitting">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="submitting ? 'Mengirim Bukti Pembayaran...' : ('Kirim Bukti Pembayaran ' + (selectedDueIds.length > 0 ? ('(' + formatRupiah(totalAmount) + ')') : ''))"></span>
                        </button>
                        <button type="button" @click="paymentModalOpen = false" class="py-2.5 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
                @else
                <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-center space-y-2">
                    <svg class="w-8 h-8 text-emerald-700 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-semibold text-emerald-900 text-xs">Semua Kewajiban Kas Anda Lunas!</p>
                    <p class="text-[11px] text-emerald-800">Tidak ada tunggakan kas yang perlu disetor saat ini.</p>
                    <button type="button" @click="paymentModalOpen = false" class="mt-2 py-1.5 px-3 text-xs font-medium rounded-lg text-zinc-700 bg-white border border-emerald-200 hover:bg-emerald-100 transition cursor-pointer">
                        Tutup
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</body>
</html>
