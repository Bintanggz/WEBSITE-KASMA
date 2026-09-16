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
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200" 
             @click.away="proofModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                    <h3 class="font-semibold text-stone-900 text-sm" x-text="'Bukti Pembayaran - ' + (proofModalData?.name || '')"></h3>
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
                
                <div class="w-full h-44 bg-stone-100 border border-stone-200 rounded-lg flex flex-col items-center justify-center p-4 text-center">
                    <svg class="w-10 h-10 text-stone-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-xs text-stone-500 font-medium">Gambar Bukti Transfer / Nota</p>
                    <p class="text-[11px] text-stone-400 mt-1">Terverifikasi &bull; Berkas PNG/JPG 240 KB</p>
                </div>

                @if($role === 'bendahara')
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="proofModalOpen = false" class="flex-1 py-2 px-3 text-xs font-medium rounded-lg text-emerald-800 bg-emerald-100 hover:bg-emerald-200 transition">
                        Setujui Pembayaran
                    </button>
                    <button type="button" @click="proofModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition">
                        Tolak
                    </button>
                </div>
                @else
                <div class="pt-2">
                    <button type="button" @click="proofModalOpen = false" class="w-full py-2 px-3 text-xs font-medium rounded-lg text-stone-700 bg-stone-100 hover:bg-stone-200 transition">
                        Tutup
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Student Payment Modal -->
    <div x-show="paymentModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/40"
         @keydown.escape.window="paymentModalOpen = false">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-stone-200" 
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
                        <span class="text-[10px] bg-emerald-50 text-emerald-800 px-1.5 py-0.5 rounded font-medium">Disalin</span>
                    </div>
                    <p class="text-[11px] text-emerald-800/90 pt-1">Iuran: <strong>Rp 10.000 / pekan</strong> (Bisa bayar langsung beberapa pekan).</p>
                </div>

                <!-- Form Upload Placeholder -->
                <div class="space-y-2.5">
                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Pilih Pekan Iuran</label>
                        <select class="w-full bg-stone-50 border border-stone-200 rounded-lg px-3 py-2 text-xs text-stone-800 focus:outline-none focus:border-stone-400">
                            <option>Pekan 9 &bull; Rp 10.000</option>
                            <option>Pekan 9 & 10 (2 Pekan) &bull; Rp 20.000</option>
                            <option>Pekan 9, 10, 11 (3 Pekan) &bull; Rp 30.000</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-stone-700 font-medium mb-1">Upload Bukti Transfer</label>
                        <div class="border-2 border-dashed border-stone-200 rounded-lg p-4 text-center hover:border-stone-300 transition cursor-pointer">
                            <svg class="w-6 h-6 mx-auto text-stone-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <p class="text-[11px] text-stone-600 font-medium">Pilih gambar bukti transfer (JPG / PNG)</p>
                            <p class="text-[10px] text-stone-400">Maksimal 5MB</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="button" @click="paymentModalOpen = false" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-emerald-800 hover:bg-emerald-900 transition">
                        Kirim Bukti Pembayaran
                    </button>
                    <button type="button" @click="paymentModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
