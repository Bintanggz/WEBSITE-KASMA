<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Kas Kelas TI26A3 &mdash; KASMA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="{{ asset('images/logo-udb.png') }}">
    
    @vite(['resources/css/app.css'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt !important;
            }
            .page-break {
                page-break-before: always;
            }
            @page {
                size: A4;
                margin: 1.5cm 1.5cm 1.5cm 1.5cm;
            }
        }
    </style>
</head>
<body class="bg-stone-100 text-stone-900 font-sans antialiased min-h-screen py-6 print:py-0 print:bg-white">

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a href="{{ route('bendahara.laporan.index') }}" 
           class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg text-stone-700 bg-white border border-stone-300 hover:bg-stone-50 transition shadow-2xs">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Laporan</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-xs cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Dokumen / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Paper Container -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 shadow-sm rounded-xl border border-stone-200 print:shadow-none print:border-none print:p-0">

        <!-- KOP Surat Resmi -->
        <div class="border-b-2 border-stone-900 pb-3 mb-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-udb.png') }}" alt="Logo Universitas Duta Bangsa" class="w-16 h-16 object-contain shrink-0">
                <div class="flex-1 text-center">
                    <h2 class="text-xs uppercase tracking-widest font-semibold text-stone-600">Universitas Duta Bangsa Surakarta</h2>
                    <h1 class="text-sm sm:text-base uppercase font-bold tracking-tight text-stone-900">Fakultas Ilmu Komputer &bull; Program Studi S1 Teknik Informatika</h1>
                    <h3 class="text-xs font-semibold text-stone-800 mt-0.5">Pengurus Kas Mahasiswa Kelas TI26A3</h3>
                    <p class="text-[10px] text-stone-500 mt-0.5">Jl. Bhayangkara No. 55, Tipes, Kec. Serengan, Kota Surakarta, Jawa Tengah 57154</p>
                </div>
            </div>
            <div class="border-t border-stone-400 mt-2.5"></div>
        </div>

        <!-- Document Heading -->
        <div class="text-center mb-6">
            <h2 class="text-base sm:text-lg font-bold text-stone-900 tracking-tight uppercase underline decoration-1 underline-offset-4">
                Laporan Pertanggungjawaban Keuangan Kas Kelas
            </h2>
            <p class="text-xs text-stone-600 mt-1 font-mono">
                Periode: Semester Genap 2025/2026 &bull; Tanggal Dokumen: {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>

        <!-- 1. Ringkasan Kas -->
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-stone-800 mb-2 border-l-2 border-stone-800 pl-2">
                I. Ringkasan Neraca Kas Kelas
            </h3>
            <table class="w-full text-xs border border-stone-300">
                <tbody>
                    <tr class="border-b border-stone-200">
                        <td class="py-2 px-3 font-semibold bg-stone-50 w-1/3">Total Penerimaan / Kas Masuk</td>
                        <td class="py-2 px-3 font-mono font-bold text-emerald-800">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-[11px] text-stone-500">Iuran mingguan mahasiswa &amp; penerimaan manual</td>
                    </tr>
                    <tr class="border-b border-stone-200">
                        <td class="py-2 px-3 font-semibold bg-stone-50">Total Pengeluaran / Belanja</td>
                        <td class="py-2 px-3 font-mono font-bold text-rose-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 text-[11px] text-stone-500">Belanja operasional, kegiatan &amp; konsumsi kelas</td>
                    </tr>
                    <tr class="bg-stone-100/70">
                        <td class="py-2.5 px-3 font-bold text-stone-900">Saldo Kas Bersih Riil (Kas di Tangan/Bank)</td>
                        <td class="py-2.5 px-3 font-mono font-bold text-sm text-stone-900">Rp {{ number_format($currentBalance, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 text-[11px] font-semibold text-stone-700">Akumulasi Saldo Aktif Saat Ini</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 2. Rekapitulasi Iuran Mingguan -->
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-stone-800 mb-2 border-l-2 border-stone-800 pl-2">
                II. Rekapitulasi Pengumpulan Iuran per Pekan (Target Siswa: {{ $totalActiveStudents }} Orang)
            </h3>
            <table class="w-full text-[11px] border border-stone-300 text-left">
                <thead class="bg-stone-100 uppercase font-semibold text-stone-700 border-b border-stone-300">
                    <tr>
                        <th class="py-2 px-2.5">Pekan</th>
                        <th class="py-2 px-2.5">Jatuh Tempo</th>
                        <th class="py-2 px-2.5 text-center">Lunas</th>
                        <th class="py-2 px-2.5 text-center">Tertunggak</th>
                        <th class="py-2 px-2.5 text-right">Terkumpul</th>
                        <th class="py-2 px-2.5 text-right">Target</th>
                        <th class="py-2 px-2.5 text-center">Capaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 font-mono">
                    @foreach($weeklySummaries as $item)
                        <tr>
                            <td class="py-1.5 px-2.5 font-sans font-semibold text-stone-900">{{ $item->period->name }}</td>
                            <td class="py-1.5 px-2.5 text-stone-600">{{ $item->period->due_date ? $item->period->due_date->format('d/m/Y') : '-' }}</td>
                            <td class="py-1.5 px-2.5 text-center font-bold text-emerald-800">{{ $item->paid_count }}</td>
                            <td class="py-1.5 px-2.5 text-center {{ $item->unpaid_count > 0 ? 'text-rose-700 font-bold' : 'text-stone-400' }}">{{ $item->unpaid_count }}</td>
                            <td class="py-1.5 px-2.5 text-right font-semibold">Rp {{ number_format($item->collected_amount, 0, ',', '.') }}</td>
                            <td class="py-1.5 px-2.5 text-right text-stone-500">Rp {{ number_format($item->target_amount, 0, ',', '.') }}</td>
                            <td class="py-1.5 px-2.5 text-center font-sans font-semibold {{ $item->percentage >= 100 ? 'text-emerald-800' : 'text-stone-700' }}">{{ $item->percentage }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 3. Rincian Mutasi Pembukuan Kas Terkini -->
        <div class="mb-8">
            <h3 class="text-xs font-bold uppercase tracking-wider text-stone-800 mb-2 border-l-2 border-stone-800 pl-2">
                III. Catatan Mutasi Transaksi Pembukuan Terkini
            </h3>
            <table class="w-full text-[10px] border border-stone-300 text-left">
                <thead class="bg-stone-100 uppercase font-semibold text-stone-700 border-b border-stone-300">
                    <tr>
                        <th class="py-1.5 px-2 w-8 text-center">No</th>
                        <th class="py-1.5 px-2 w-20">Tanggal</th>
                        <th class="py-1.5 px-2 w-20">Arus</th>
                        <th class="py-1.5 px-2 w-24">Kategori</th>
                        <th class="py-1.5 px-2">Uraian / Keterangan</th>
                        <th class="py-1.5 px-2 text-right w-24">Pemasukan</th>
                        <th class="py-1.5 px-2 text-right w-24">Pengeluaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 font-mono">
                    @forelse($recentTransactions as $index => $tx)
                        <tr>
                            <td class="py-1.5 px-2 text-center text-stone-500">{{ $index + 1 }}</td>
                            <td class="py-1.5 px-2 text-stone-700">{{ $tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') : '-' }}</td>
                            <td class="py-1.5 px-2 font-sans font-semibold {{ $tx->type === 'income' ? 'text-emerald-800' : 'text-rose-700' }}">
                                {{ $tx->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </td>
                            <td class="py-1.5 px-2 text-stone-600 font-sans truncate max-w-[100px]">{{ $tx->category ?? '-' }}</td>
                            <td class="py-1.5 px-2 font-sans text-stone-800">{{ $tx->description }}</td>
                            <td class="py-1.5 px-2 text-right font-semibold text-emerald-800">
                                {{ $tx->type === 'income' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-1.5 px-2 text-right font-semibold text-rose-700">
                                {{ $tx->type === 'expense' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center font-sans text-stone-400">Belum ada catatan mutasi transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 4. Bagian Pengesahan / Tanda Tangan -->
        <div class="pt-6 border-t border-stone-300 text-xs">
            <div class="flex justify-between items-start">
                <!-- Kiri: Mengetahui Ketua Kelas -->
                <div class="text-center w-56">
                    <p class="text-stone-600">Mengetahui,</p>
                    <p class="font-bold text-stone-900">Ketua Kelas TI26A3</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-stone-900 border-b border-stone-800 pb-0.5 inline-block min-w-40">
                        Hafizh Al-Fatih
                    </p>
                    <p class="text-[10px] text-stone-500 font-mono mt-0.5">NIM. 220401</p>
                </div>

                <!-- Kanan: Dibuat oleh Bendahara Kelas -->
                <div class="text-center w-56">
                    <p class="text-stone-600">Surakarta, {{ now()->translatedFormat('d F Y') }}</p>
                    <p class="font-bold text-stone-900">Bendahara Kelas TI26A3</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-stone-900 border-b border-stone-800 pb-0.5 inline-block min-w-40">
                        {{ $bendahara->name ?? 'Nadya Putri' }}
                    </p>
                    <p class="text-[10px] text-stone-500 font-mono mt-0.5">NIM. {{ $bendahara->nim ?? '220400' }}</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
