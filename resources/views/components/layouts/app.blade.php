@props([
    'role' => 'mahasiswa',
    'title' => 'KASMA'
])
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="KASMA - Sistem Manajemen Uang Kas Mahasiswa yang transparan, rapi, dan mudah digunakan.">
    <title>{{ $title }} &mdash; KASMA</title>
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-stone-800 bg-[#FAF9F5] selection:bg-emerald-100 selection:text-emerald-900"
      x-data="{ 
          mobileMenuOpen: false, 
          proofModalOpen: false,
          paymentModalOpen: false,
          proofModalData: null,
          openProof(data) {
              this.proofModalData = data;
              this.proofModalOpen = true;
          }
      }">

    <!-- Mobile Slide-out Drawer & Overlay -->
    <x-mobile-nav :role="$role" />

    <div class="min-h-full flex">
        <!-- Desktop Sidebar -->
        <x-sidebar :role="$role" />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 md:pl-64">
            <!-- Top Navigation Bar (No role switcher) -->
            <x-topbar :role="$role" />

            <!-- Page Content -->
            <main class="flex-1 pb-24 md:pb-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
                    
                    <!-- Flash Notification Banners -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between text-xs sm:text-sm shadow-2xs">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                            <button type="button" @click="show = false" class="text-emerald-700 hover:text-emerald-900 text-base font-bold ml-2 leading-none cursor-pointer" aria-label="Tutup">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center justify-between text-xs sm:text-sm shadow-2xs">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                            <button type="button" @click="show = false" class="text-rose-700 hover:text-rose-900 text-base font-bold ml-2 leading-none cursor-pointer" aria-label="Tutup">&times;</button>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div x-data="{ show: true }" x-show="show" class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm shadow-2xs">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <span class="font-semibold block mb-1">Periksa isian formulir:</span>
                                        <ul class="list-disc list-inside space-y-0.5 text-rose-800 text-xs">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button type="button" @click="show = false" class="text-rose-700 hover:text-rose-900 text-base font-bold ml-2 leading-none cursor-pointer" aria-label="Tutup">&times;</button>
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
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
         @keydown.escape.window="proofModalOpen = false">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200 max-h-[90vh] overflow-y-auto" 
             @click.away="proofModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <h3 class="font-semibold text-stone-900 text-sm" x-text="'Bukti Transaksi - ' + (proofModalData?.name || '')"></h3>
                </div>
                <button type="button" @click="proofModalOpen = false" class="text-stone-400 hover:text-stone-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-4 space-y-3">
                <div class="bg-stone-50 border border-stone-200 rounded-lg p-3 text-xs space-y-1 text-stone-600">
                    <div class="flex justify-between">
                        <span>Nominal:</span>
                        <span class="font-semibold text-stone-900 font-mono" x-text="proofModalData?.amount"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Metode:</span>
                        <span class="font-medium text-stone-800" x-text="proofModalData?.method"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Waktu:</span>
                        <span class="text-stone-600" x-text="proofModalData?.time"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Keterangan:</span>
                        <span class="font-medium text-emerald-800" x-text="proofModalData?.weeks"></span>
                    </div>
                </div>
                
                <template x-if="proofModalData?.proof_url">
                    <div class="w-full max-h-64 overflow-hidden rounded-lg border border-stone-200 bg-stone-100 flex items-center justify-center">
                        <img :src="proofModalData.proof_url" :alt="proofModalData?.name" class="w-full max-h-64 object-contain rounded" />
                    </div>
                </template>

                <template x-if="!proofModalData?.proof_url">
                    <div class="w-full h-32 bg-stone-50 border border-stone-200 rounded-lg flex flex-col items-center justify-center p-4 text-center">
                        <svg class="w-8 h-8 text-stone-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-xs text-stone-600 font-medium">Transaksi Tunai / Tanpa Berkas Digital</p>
                        <p class="text-[11px] text-stone-400 mt-0.5">Tercatat resmi dalam buku kas kelas</p>
                    </div>
                </template>

                @if($role === 'bendahara')
                <template x-if="proofModalData?.is_pending && proofModalData?.payment_id">
                    <div class="flex gap-2 pt-2">
                        <form :action="'/bendahara/payments/' + proofModalData.payment_id + '/approve'" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-2 px-3 text-xs font-semibold rounded-lg text-emerald-800 bg-emerald-100 hover:bg-emerald-200 transition text-center">
                                Setujui Pembayaran
                            </button>
                        </form>
                        <button type="button" 
                                @click="proofModalOpen = false; window.dispatchEvent(new CustomEvent('open-reject-modal', { detail: { id: proofModalData.payment_id, name: proofModalData.name } }))" 
                                class="py-2 px-3 text-xs font-medium rounded-lg text-rose-700 bg-rose-50 hover:bg-rose-100 transition">
                            Tolak
                        </button>
                    </div>
                </template>
                @endif

                <div class="pt-2">
                    <button type="button" @click="proofModalOpen = false" class="w-full py-2 px-3 text-xs font-medium rounded-lg text-stone-700 bg-stone-100 hover:bg-stone-200 transition">
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
            ->with(['cashPeriod', 'pendingPayment'])
            ->get()
            ->sortBy(fn($d) => $d->cashPeriod->week_number ?? 0);
    @endphp
    <div x-show="paymentModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
         @keydown.escape.window="paymentModalOpen = false"
         x-data="{
             copied: false,
             fileName: '',
             copyAccount() {
                 navigator.clipboard.writeText('8730192819');
                 this.copied = true;
                 setTimeout(() => this.copied = false, 2500);
             }
         }">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200 max-h-[90vh] overflow-y-auto" 
             @click.away="paymentModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <h3 class="font-semibold text-stone-900 text-sm">Bayar Kas Kelas TI-3A</h3>
                </div>
                <button type="button" @click="paymentModalOpen = false" class="text-stone-400 hover:text-stone-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="mt-4 space-y-4 text-xs">
                <!-- Info Rekening -->
                <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-lg text-emerald-950 space-y-1.5">
                    <p class="font-semibold text-[11px] uppercase tracking-wider text-emerald-800">Transfer Rekening Resmi Kas</p>
                    <div class="flex justify-between items-center bg-white p-2.5 rounded border border-emerald-200/80">
                        <div>
                            <span class="text-[10px] text-stone-500 block">Bank Central Asia (BCA)</span>
                            <span class="font-mono font-bold text-stone-900 text-xs">873-019-2819</span>
                            <span class="text-[10px] text-stone-500 block">a.n. Bendahara Kas TI-3A</span>
                        </div>
                        <button type="button" @click="copyAccount()" class="text-[10px] bg-emerald-50 hover:bg-emerald-100 text-emerald-800 px-2 py-1 rounded font-semibold border border-emerald-200 transition cursor-pointer">
                            <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-emerald-800/90 pt-0.5">Iuran: <strong>Rp 10.000 / pekan</strong> (Bisa pilih beberapa pekan sekaligus).</p>
                </div>

                @if($modalUnpaidDues->count() > 0)
                <form method="POST" action="{{ route('mahasiswa.payments.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Pilih Pekan Iuran yang Dibayar</label>
                        <div class="border border-stone-200 rounded-lg p-2.5 max-h-36 overflow-y-auto space-y-1.5 bg-stone-50/60">
                            @foreach($modalUnpaidDues as $due)
                                @php $isPending = $due->pendingPayment !== null; @endphp
                                <label class="flex items-center justify-between p-1.5 rounded hover:bg-white transition {{ $isPending ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" 
                                               name="student_due_ids[]" 
                                               value="{{ $due->id }}" 
                                               {{ $isPending ? 'disabled' : '' }}
                                               class="rounded border-stone-300 text-emerald-700 focus:ring-emerald-600">
                                        <span class="text-stone-800 font-medium">{{ $due->cashPeriod->name ?? ('Pekan ' . $due->cashPeriod->week_number) }}</span>
                                    </div>
                                    <div class="text-right">
                                        @if($isPending)
                                            <span class="text-[10px] text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">Menunggu Verifikasi</span>
                                        @else
                                            <span class="font-mono text-stone-700 font-semibold">Rp {{ number_format($due->amount, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-stone-400 mt-1">Centang satu atau beberapa pekan yang ingin Anda setorkan sekaligus.</p>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Metode Pembayaran</label>
                        <select name="payment_method" class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400" required>
                            <option value="bank_transfer">Transfer Bank (BCA Kas)</option>
                            <option value="qris">QRIS Kas Kelas</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Upload Bukti Transfer</label>
                        <label class="border-2 border-dashed border-stone-200 rounded-lg p-4 text-center hover:border-stone-300 transition cursor-pointer block bg-stone-50/40">
                            <input type="file" name="proof_file" accept="image/*" class="hidden" required @change="fileName = $event.target.files[0]?.name">
                            <svg class="w-6 h-6 mx-auto text-stone-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-[11px] text-stone-600 font-medium" x-text="fileName ? fileName : 'Pilih gambar bukti transfer (JPG / PNG)'"></p>
                            <p class="text-[10px] text-stone-400 mt-0.5" x-show="!fileName">Format JPG, PNG, atau WEBP &bull; Maksimal 5MB</p>
                        </label>
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-stone-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-800 hover:bg-emerald-900 transition shadow-2xs">
                            Kirim Bukti Pembayaran
                        </button>
                        <button type="button" @click="paymentModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition">
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
                    <button type="button" @click="paymentModalOpen = false" class="mt-2 py-1.5 px-3 text-xs font-medium rounded-lg text-stone-700 bg-white border border-emerald-200 hover:bg-emerald-100 transition">
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
