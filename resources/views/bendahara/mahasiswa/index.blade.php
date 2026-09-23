<x-layouts.app role="bendahara" title="Kelola Data Mahasiswa">

    <div x-data="{
        addModalOpen: false,
        editModalOpen: false,
        statusModalOpen: false,
        activationModalOpen: {{ session('new_student_activation') ? 'true' : 'false' }},
        selectedStudent: {
            id: null,
            name: '',
            nim: '',
            email: '',
            phone_number: '',
            is_active: true
        },
        copied: false,
        copiedMsg: false,
        copyToClipboard(text, isMessage = false) {
            navigator.clipboard.writeText(text).then(() => {
                if (isMessage) {
                    this.copiedMsg = true;
                    setTimeout(() => this.copiedMsg = false, 2500);
                } else {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                }
            });
        },
        openEdit(student) {
            this.selectedStudent = Object.assign({}, student);
            this.editModalOpen = true;
        },
        openStatusConfirm(student) {
            this.selectedStudent = Object.assign({}, student);
            this.statusModalOpen = true;
        }
    }">

        <!-- Header Banner -->
        <div class="mb-6 pb-4 border-b border-stone-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-stone-900 tracking-tight">Kelola Data Mahasiswa</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-stone-100 text-stone-700 border border-stone-200">
                            {{ $totalStudents }} Mahasiswa Terdaftar
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Kelola akun mahasiswa kelas, buat tautan aktivasi mandiri, dan atur status akses ke sistem kas.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="addModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white bg-stone-900 hover:bg-stone-800 transition shadow-2xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Mahasiswa</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Feedback Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200/80 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs sm:text-sm text-emerald-900">
                    <p class="font-semibold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200/80 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs sm:text-sm text-rose-900">
                    <p class="font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200/80">
                <p class="text-xs sm:text-sm font-semibold text-rose-900 mb-1">Terjadi kesalahan pada input data:</p>
                <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Total Mahasiswa</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-900 mt-2 block">
                    {{ $totalStudents }}
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Seluruh data kelas TI26A3</span>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Mahasiswa Aktif</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-emerald-800 mt-2 block">
                    {{ $activeStudents }}
                </span>
                <span class="text-[11px] text-stone-500 mt-1 block">Menerima kewajiban iuran kas</span>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Menunggu Aktivasi</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-amber-700 mt-2 block">
                    {{ $pendingActivationCount }}
                </span>
                <span class="text-[11px] text-amber-600 mt-1 block">Belum membuat kata sandi</span>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-xl border border-stone-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Mahasiswa Nonaktif</span>
                <span class="text-2xl sm:text-3xl font-bold font-mono text-stone-500 mt-2 block">
                    {{ $inactiveStudents }}
                </span>
                <span class="text-[11px] text-stone-400 mt-1 block">Bebas dari periode iuran baru</span>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 rounded-xl border border-stone-200/90 shadow-2xs mb-6">
            <form method="GET" action="{{ route('bendahara.mahasiswa.index') }}" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                <div class="flex-1 flex flex-col sm:flex-row gap-2.5">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Cari berdasarkan nama, NIM, atau email..." 
                               class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm bg-stone-50 border border-stone-200 rounded-lg text-stone-900 placeholder-stone-400 focus:outline-none focus:ring-1 focus:ring-stone-800 focus:bg-white transition">
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full sm:w-48">
                        <select name="status" 
                                onchange="this.form.submit()" 
                                class="w-full px-3 py-2 text-xs sm:text-sm bg-stone-50 border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-800 focus:bg-white transition cursor-pointer">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Hanya Aktif</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Hanya Nonaktif</option>
                            <option value="pending_activation" {{ $status === 'pending_activation' ? 'selected' : '' }}>Menunggu Aktivasi</option>
                            <option value="activated" {{ $status === 'activated' ? 'selected' : '' }}>Sudah Diaktifkan</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-800 transition cursor-pointer">
                        Filter
                    </button>
                    @if(!empty($search) || $status !== 'all')
                        <a href="{{ route('bendahara.mahasiswa.index') }}" class="px-3 py-2 text-xs font-medium text-stone-500 hover:text-stone-800 transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Student Listing Table -->
        <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-stone-50/80 border-b border-stone-200/80 text-[11px] font-semibold text-stone-500 uppercase tracking-wider">
                            <th scope="col" class="py-3.5 px-4">Mahasiswa</th>
                            <th scope="col" class="py-3.5 px-4">NIM</th>
                            <th scope="col" class="py-3.5 px-4">Kontak</th>
                            <th scope="col" class="py-3.5 px-4 text-center">Status Akun</th>
                            <th scope="col" class="py-3.5 px-4 text-center">Status Aktivasi</th>
                            <th scope="col" class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-stone-700">
                        @forelse($students as $student)
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-stone-100 border border-stone-200 text-stone-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($student->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-stone-900 truncate">{{ $student->name }}</p>
                                            <p class="text-[11px] text-stone-400">Kelas TI26A3</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono font-medium text-stone-800 whitespace-nowrap">
                                    {{ $student->nim }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <p class="text-xs text-stone-800">{{ $student->email }}</p>
                                    <p class="text-[11px] text-stone-400 font-mono">{{ $student->phone_number ?? '-' }}</p>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($student->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-stone-100 text-stone-600 border border-stone-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($student->isActivated())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200/70" title="Diaktifkan pada {{ $student->activated_at?->format('d M Y H:i') }}">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>Aktif</span>
                                        </span>
                                    @elseif($student->isActivationExpired())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-50 text-rose-800 border border-rose-200/70" title="Kedaluwarsa pada {{ $student->activation_expires_at?->format('d M Y H:i') }}">
                                            <svg class="w-3 h-3 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Kedaluwarsa</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200/70" title="Berlaku s.d. {{ $student->activation_expires_at?->format('d M Y H:i') }}">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>Menunggu</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Resend Activation (if not activated) -->
                                        @if(!$student->isActivated())
                                            <form method="POST" action="{{ route('bendahara.mahasiswa.resend-activation', $student) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        title="Kirim Ulang Tautan Aktivasi"
                                                        class="p-1.5 text-stone-600 hover:text-stone-900 hover:bg-stone-100 rounded-lg transition cursor-pointer">
                                                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Edit button -->
                                        <button type="button" 
                                                @click="openEdit({
                                                    id: {{ $student->id }},
                                                    name: '{{ addslashes($student->name) }}',
                                                    nim: '{{ $student->nim }}',
                                                    email: '{{ $student->email }}',
                                                    phone_number: '{{ $student->phone_number ?? '' }}',
                                                    is_active: {{ $student->is_active ? 'true' : 'false' }}
                                                })"
                                                title="Edit Mahasiswa"
                                                class="p-1.5 text-stone-500 hover:text-stone-900 hover:bg-stone-100 rounded-lg transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <!-- Toggle Status button -->
                                        <button type="button" 
                                                @click="openStatusConfirm({
                                                    id: {{ $student->id }},
                                                    name: '{{ addslashes($student->name) }}',
                                                    nim: '{{ $student->nim }}',
                                                    is_active: {{ $student->is_active ? 'true' : 'false' }}
                                                })"
                                                title="{{ $student->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                class="p-1.5 rounded-lg transition cursor-pointer {{ $student->is_active ? 'text-stone-400 hover:text-rose-700 hover:bg-rose-50' : 'text-emerald-600 hover:bg-emerald-50' }}">
                                            @if($student->is_active)
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-stone-500">
                                    <div class="max-w-xs mx-auto space-y-2">
                                        <svg class="w-8 h-8 text-stone-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="text-xs font-semibold text-stone-700">Tidak ada mahasiswa yang ditemukan</p>
                                        <p class="text-[11px] text-stone-400">Silakan sesuaikan kata kunci pencarian atau status filter Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="p-4 border-t border-stone-100">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- MODAL: Tambah Mahasiswa Baru -->
        <!-- ========================================== -->
        <div x-show="addModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs transition-opacity"
                 x-show="addModalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="addModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="addModalOpen"
                     x-transition:enter="ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-stone-200/90">
                    
                    <form method="POST" action="{{ route('bendahara.mahasiswa.store') }}">
                        @csrf
                        <div class="p-5 sm:p-6 space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-stone-100 text-stone-700 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm sm:text-base font-bold text-stone-900">Tambah Mahasiswa Baru</h3>
                                </div>
                                <button type="button" @click="addModalOpen = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="p-3 rounded-lg bg-stone-50 border border-stone-200/80 text-[11px] text-stone-600 space-y-1">
                                <p class="font-semibold text-stone-800">Prinsip Keamanan Akun:</p>
                                <p>Peran akun otomatis sebagai <strong class="text-stone-900">Mahasiswa</strong>. Mahasiswa akan menentukan kata sandi sendiri melalui tautan aktivasi mandiri.</p>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Lengkap <span class="text-rose-600">*</span></label>
                                    <input type="text" name="name" required placeholder="Contoh: Muhammad Reza" value="{{ old('name') }}"
                                           class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nomor Induk Mahasiswa (NIM) <span class="text-rose-600">*</span></label>
                                    <input type="text" name="nim" required placeholder="Contoh: 220401033" value="{{ old('nim') }}"
                                           class="w-full px-3 py-2 text-xs sm:text-sm font-mono bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Alamat Email <span class="text-rose-600">*</span></label>
                                    <input type="email" name="email" required placeholder="Contoh: reza@student.ac.id" value="{{ old('email') }}"
                                           class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nomor WhatsApp / HP <span class="text-rose-600">*</span></label>
                                    <input type="text" name="phone_number" required placeholder="Contoh: 081234567890" value="{{ old('phone_number') }}"
                                           class="w-full px-3 py-2 text-xs sm:text-sm font-mono bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                                    <span class="text-[10px] text-stone-400 mt-0.5 block">Digunakan untuk kemudahan kirim tautan aktivasi via WhatsApp.</span>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="addModalOpen = false" class="px-3.5 py-2 text-xs font-medium text-stone-600 hover:text-stone-800 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-stone-900 hover:bg-stone-800 rounded-lg shadow-2xs transition">
                                Simpan & Buat Tautan Aktivasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: Edit Data Mahasiswa -->
        <!-- ========================================== -->
        <div x-show="editModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs transition-opacity"
                 x-show="editModalOpen"
                 @click="editModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="editModalOpen"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-stone-200/90">
                    
                    <form method="POST" :action="'/bendahara/mahasiswa/' + selectedStudent.id">
                        @csrf
                        @method('PUT')
                        <div class="p-5 sm:p-6 space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                                <h3 class="text-sm sm:text-base font-bold text-stone-900">Edit Data Mahasiswa</h3>
                                <button type="button" @click="editModalOpen = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">NIM (Identitas Tetap)</label>
                                    <input type="text" :value="selectedStudent.nim" disabled
                                           class="w-full px-3 py-2 text-xs sm:text-sm font-mono bg-stone-100 border border-stone-200 rounded-lg text-stone-500 cursor-not-allowed">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Email (Identitas Akun)</label>
                                    <input type="text" :value="selectedStudent.email" disabled
                                           class="w-full px-3 py-2 text-xs sm:text-sm bg-stone-100 border border-stone-200 rounded-lg text-stone-500 cursor-not-allowed">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nama Lengkap <span class="text-rose-600">*</span></label>
                                    <input type="text" name="name" required x-model="selectedStudent.name"
                                           class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nomor WhatsApp / HP</label>
                                    <input type="text" name="phone_number" x-model="selectedStudent.phone_number"
                                           class="w-full px-3 py-2 text-xs sm:text-sm font-mono bg-white border border-stone-200 rounded-lg text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-800">
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="editModalOpen = false" class="px-3.5 py-2 text-xs font-medium text-stone-600 hover:text-stone-800 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-stone-900 hover:bg-stone-800 rounded-lg shadow-2xs transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: Konfirmasi Ubah Status Aktif/Nonaktif -->
        <!-- ========================================== -->
        <div x-show="statusModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs transition-opacity"
                 x-show="statusModalOpen"
                 @click="statusModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="statusModalOpen"
                     class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-stone-200/90">
                    
                    <form method="POST" :action="'/bendahara/mahasiswa/' + selectedStudent.id + '/toggle-status'">
                        @csrf
                        @method('PATCH')
                        <div class="p-5 sm:p-6 space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                     :class="selectedStudent.is_active ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm sm:text-base font-bold text-stone-900" 
                                        x-text="selectedStudent.is_active ? 'Nonaktifkan Akun Mahasiswa' : 'Aktifkan Kembali Mahasiswa'"></h3>
                                    <p class="text-xs text-stone-500 font-mono" x-text="selectedStudent.name + ' (' + selectedStudent.nim + ')'"></p>
                                </div>
                            </div>

                            <p class="text-xs text-stone-600 leading-relaxed" 
                               x-show="selectedStudent.is_active">
                                Mahasiswa nonaktif tidak dapat masuk ke aplikasi kas dan <strong class="text-stone-900">tidak akan menerima kewajiban iuran pekan baru</strong>. Riwayat pembayaran dan tunggakan yang telah ada tetap terjaga dengan aman di sistem.
                            </p>

                            <p class="text-xs text-stone-600 leading-relaxed" 
                               x-show="!selectedStudent.is_active">
                                Mahasiswa akan dapat masuk kembali dan berpartisipasi dalam pembayaran iuran kas kelas.
                            </p>
                        </div>

                        <div class="px-5 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="statusModalOpen = false" class="px-3.5 py-2 text-xs font-medium text-stone-600 hover:text-stone-800 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 text-xs font-semibold text-white rounded-lg shadow-2xs transition"
                                    :class="selectedStudent.is_active ? 'bg-rose-700 hover:bg-rose-800' : 'bg-emerald-700 hover:bg-emerald-800'"
                                    x-text="selectedStudent.is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan Kembali'">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: Informasi Tautan Aktivasi Mahasiswa -->
        <!-- ========================================== -->
        @if(session('new_student_activation'))
            @php
                $actData = session('new_student_activation');
                $waPhone = preg_replace('/[^0-9]/', '', $actData['phone_number'] ?? '');
                if (str_starts_with($waPhone, '0')) {
                    $waPhone = '62' . substr($waPhone, 1);
                }
                $waUrl = 'https://wa.me/' . $waPhone . '?text=' . urlencode($actData['whatsapp_message'] ?? '');
            @endphp
            <div x-show="activationModalOpen" 
                 x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto"
                 aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs transition-opacity"
                     x-show="activationModalOpen"
                     @click="activationModalOpen = false"></div>

                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="activationModalOpen"
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-stone-200/90">
                        
                        <div class="p-5 sm:p-6 space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-800 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm sm:text-base font-bold text-stone-900">Tautan Aktivasi Akun Siap</h3>
                                        <p class="text-[11px] text-stone-500">Berikan tautan ini kepada mahasiswa terkait.</p>
                                    </div>
                                </div>
                                <button type="button" @click="activationModalOpen = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Mahasiswa Info Box -->
                            <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80 space-y-1 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-stone-500">Nama Mahasiswa:</span>
                                    <strong class="text-stone-900">{{ $actData['name'] }}</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-stone-500">NIM:</span>
                                    <strong class="text-stone-900 font-mono">{{ $actData['nim'] }}</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-stone-500">Email:</span>
                                    <span class="text-stone-700">{{ $actData['email'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-stone-500">No. WhatsApp:</span>
                                    <span class="text-stone-700 font-mono">{{ $actData['phone_number'] }}</span>
                                </div>
                            </div>

                            <!-- Activation Link Box -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-semibold text-stone-700">Tautan Aktivasi Mandiri (72 Jam):</label>
                                <div class="flex gap-2">
                                    <input type="text" readonly value="{{ $actData['activation_url'] }}" 
                                           class="w-full px-3 py-2 text-xs font-mono bg-stone-50 border border-stone-200 rounded-lg text-stone-800 select-all focus:outline-none">
                                    <button type="button" 
                                            @click="copyToClipboard('{{ addslashes($actData['activation_url']) }}')"
                                            class="px-3 py-2 text-xs font-semibold rounded-lg bg-stone-900 text-white hover:bg-stone-800 transition shrink-0 cursor-pointer">
                                        <span x-show="!copied">Salin</span>
                                        <span x-show="copied" class="text-emerald-300">Tersalin!</span>
                                    </button>
                                </div>
                            </div>

                            <!-- WhatsApp Action Box -->
                            <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/70 space-y-2">
                                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-950">
                                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.087.087-.179.182-.077.357.101.174.449.741.964 1.2.662.59 1.221.774 1.394.86.173.087.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                                    </svg>
                                    <span>Kirim Otomatis ke WhatsApp</span>
                                </div>
                                <p class="text-[11px] text-emerald-800">
                                    Pesan sudah disiapkan dengan format rapi dan menyertakan tautan aktivasi di atas.
                                </p>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    <a href="{{ $waUrl }}" target="_blank" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white transition shadow-2xs">
                                        <span>Buka WhatsApp Web / App</span>
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <button type="button" 
                                            @click="copyToClipboard('{{ addslashes($actData['whatsapp_message'] ?? '') }}', true)"
                                            class="px-3 py-1.5 text-xs font-medium rounded-lg bg-white border border-emerald-300 text-emerald-900 hover:bg-emerald-50 transition cursor-pointer">
                                        <span x-show="!copiedMsg">Salin Teks Pesan</span>
                                        <span x-show="copiedMsg" class="text-emerald-700 font-semibold">Pesan Tersalin!</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="px-5 py-4 bg-stone-50 border-t border-stone-100 flex items-center justify-end">
                            <button type="button" @click="activationModalOpen = false" class="px-4 py-2 text-xs font-semibold text-stone-800 bg-white border border-stone-200 hover:bg-stone-50 rounded-lg shadow-2xs">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

</x-layouts.app>
