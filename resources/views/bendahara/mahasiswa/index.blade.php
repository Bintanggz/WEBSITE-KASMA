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
        <div class="mb-6 pb-4 border-b border-zinc-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-bold text-zinc-900 tracking-tight">Kelola Data Mahasiswa</h2>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-700 border border-zinc-200">
                            {{ $totalStudents }} Mahasiswa Terdaftar
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-zinc-500 mt-0.5">
                        Kelola akun mahasiswa kelas, buat tautan aktivasi mandiri, dan atur status akses ke sistem kas.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="addModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Mahasiswa</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Clean Summary Metrics Strip -->
        <div class="bg-white rounded-xl border border-zinc-200 p-5 mb-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 divide-y lg:divide-y-0 lg:divide-x divide-zinc-100">
                <div class="pt-3 lg:pt-0 lg:px-4 first:lg:pl-0">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Total Mahasiswa</span>
                    <span class="text-2xl font-bold font-mono text-zinc-900 mt-1 block">
                        {{ $totalStudents }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Seluruh data kelas TI26A3</span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Mahasiswa Aktif</span>
                    <span class="text-2xl font-bold font-mono text-emerald-800 mt-1 block">
                        {{ $activeStudents }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Menerima kewajiban iuran kas</span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Menunggu Aktivasi</span>
                    <span class="text-2xl font-bold font-mono {{ $pendingActivationCount > 0 ? 'text-amber-900' : 'text-zinc-900' }} mt-1 block">
                        {{ $pendingActivationCount }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Tautan telah digenerate</span>
                </div>

                <div class="pt-3 lg:pt-0 lg:px-4">
                    <span class="text-[11px] font-medium text-zinc-500 uppercase tracking-wider block">Nonaktif</span>
                    <span class="text-2xl font-bold font-mono {{ $inactiveStudents > 0 ? 'text-rose-600' : 'text-zinc-900' }} mt-1 block">
                        {{ $inactiveStudents }}
                    </span>
                    <span class="text-[11px] text-zinc-400 mt-1 block">Tidak dialokasikan iuran baru</span>
                </div>
            </div>
        </div>

        <!-- Students Table Card -->
        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
            <div class="p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-zinc-900 text-sm tracking-tight">Daftar Mahasiswa Kelas TI26A3</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Kelola identitas, nomor WhatsApp, status aktivasi, dan kepatuhan kas</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600">
                    <thead class="bg-zinc-50 text-[11px] uppercase tracking-wider text-zinc-400 font-semibold border-b border-zinc-100">
                        <tr>
                            <th class="py-3 px-4">Nama Mahasiswa</th>
                            <th class="py-3 px-4">NIM</th>
                            <th class="py-3 px-4">Alamat Email</th>
                            <th class="py-3 px-4">Kontak WhatsApp</th>
                            <th class="py-3 px-4 text-center">Status Akun</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($students as $student)
                            @php
                                $isActivated = $student->is_activated;
                                $isActive = $student->is_active;
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-zinc-900">{{ $student->name }}</div>
                                    @if(!$isActivated)
                                        <span class="text-[10px] text-amber-800 font-medium">Belum aktivasi akun</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 font-mono text-zinc-700 whitespace-nowrap">
                                    {{ $student->nim ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono text-zinc-600">
                                    {{ $student->email }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($student->phone_number)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->phone_number) }}" 
                                           target="_blank" 
                                           class="text-zinc-700 hover:text-emerald-700 font-mono inline-flex items-center gap-1 transition">
                                            <span>{{ $student->phone_number }}</span>
                                        </a>
                                    @else
                                        <span class="text-zinc-300 font-mono">&mdash;</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if(!$isActive)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-600 border border-zinc-200">
                                            Nonaktif
                                        </span>
                                    @elseif(!$isActivated)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            Menunggu Aktivasi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$isActivated && $isActive)
                                            <!-- Resend activation link button -->
                                            <form action="{{ route('bendahara.mahasiswa.resend-activation', $student) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-2 py-1 text-[11px] font-medium rounded text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition cursor-pointer"
                                                        title="Kirim ulang token aktivasi baru">
                                                    Token Baru
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" 
                                                @click="openEdit({
                                                    id: {{ $student->id }},
                                                    name: '{{ addslashes($student->name) }}',
                                                    nim: '{{ addslashes($student->nim ?? '') }}',
                                                    email: '{{ addslashes($student->email) }}',
                                                    phone_number: '{{ addslashes($student->phone_number ?? '') }}',
                                                    is_active: {{ $student->is_active ? 'true' : 'false' }}
                                                })"
                                                class="p-1 text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 rounded transition cursor-pointer"
                                                title="Ubah Data">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <button type="button" 
                                                @click="openStatusConfirm({
                                                    id: {{ $student->id }},
                                                    name: '{{ addslashes($student->name) }}',
                                                    is_active: {{ $student->is_active ? 'true' : 'false' }}
                                                })"
                                                class="p-1 text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 rounded transition cursor-pointer"
                                                title="{{ $student->is_active ? 'Nonaktifkan Mahasiswa' : 'Aktifkan Kembali' }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-zinc-400">
                                    Belum ada data mahasiswa terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="p-3.5 bg-zinc-50 border-t border-zinc-200">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

        <!-- MODAL: Tambah Mahasiswa -->
        <div x-show="addModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="addModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="addModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="font-semibold text-zinc-900 text-sm">Tambah Data Mahasiswa Baru</h3>
                    <button type="button" @click="addModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('bendahara.mahasiswa.store') }}" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Nama Lengkap Mahasiswa</label>
                        <input type="text" name="name" placeholder="contoh: Muhammad Rizky" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Nomor Induk Mahasiswa (NIM)</label>
                        <input type="text" name="nim" placeholder="contoh: 22040105" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Alamat Email</label>
                        <input type="email" name="email" placeholder="contoh: rizky@mhs.udb.ac.id" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Nomor WhatsApp / HP (Opsional)</label>
                        <input type="text" name="phone_number" placeholder="contoh: 08123456789" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400">
                    </div>

                    <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-700 space-y-1">
                        <p class="font-semibold text-[11px] text-zinc-800">Aktivasi Mandiri Mahasiswa</p>
                        <p class="text-[11px] text-zinc-500">
                            Sistem akan otomatis membuat token aktivasi 72 jam. Anda dapat menyalin tautan dan mengirimkannya via WhatsApp kepada mahasiswa.
                        </p>
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                            Simpan &amp; Buat Tautan
                        </button>
                        <button type="button" @click="addModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Edit Mahasiswa -->
        <div x-show="editModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="editModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200 max-h-[90vh] overflow-y-auto" 
                 @click.away="editModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="font-semibold text-zinc-900 text-sm">Ubah Data Mahasiswa</h3>
                    <button type="button" @click="editModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/bendahara/mahasiswa/' + selectedStudent.id" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Nama Lengkap</label>
                        <input type="text" name="name" x-model="selectedStudent.name" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">NIM</label>
                        <input type="text" name="nim" x-model="selectedStudent.nim" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Alamat Email</label>
                        <input type="email" name="email" x-model="selectedStudent.email" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400" required>
                    </div>

                    <div>
                        <label class="block text-zinc-700 font-medium mb-1">Nomor WhatsApp</label>
                        <input type="text" name="phone_number" x-model="selectedStudent.phone_number" class="w-full bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2 text-xs text-zinc-900 focus:outline-none focus:border-zinc-400">
                    </div>

                    <div class="flex gap-2 pt-3 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-zinc-900 hover:bg-zinc-800 transition cursor-pointer">
                            Perbarui Data
                        </button>
                        <button type="button" @click="editModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Konfirmasi Ubah Status Aktif / Nonaktif -->
        <div x-show="statusModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="statusModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200" 
                 @click.away="statusModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="font-semibold text-zinc-900 text-sm">Konfirmasi Status Mahasiswa</h3>
                    <button type="button" @click="statusModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3 text-xs text-zinc-600">
                    <p>
                        Apakah Anda yakin ingin <strong x-text="selectedStudent.is_active ? 'menonaktifkan' : 'mengaktifkan kembali'"></strong> akun <strong class="text-zinc-900" x-text="selectedStudent.name"></strong>?
                    </p>
                    <p class="text-[11px] text-zinc-500 bg-zinc-50 p-2.5 rounded-lg border border-zinc-200">
                        Mahasiswa nonaktif tidak dapat masuk ke sistem dan tidak akan dialokasikan kewajiban iuran kas baru pada periode mendatang. Riwayat pembayaran terdahulu tetap terjaga aman.
                    </p>

                    <form :action="'/bendahara/mahasiswa/' + selectedStudent.id + '/toggle-status'" method="POST" class="pt-2">
                        @csrf
                        @method('PATCH')
                        <div class="flex gap-2">
                            <button type="submit" 
                                    class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white transition cursor-pointer"
                                    :class="selectedStudent.is_active ? 'bg-rose-700 hover:bg-rose-800' : 'bg-emerald-700 hover:bg-emerald-800'">
                                <span x-text="selectedStudent.is_active ? 'Ya, Nonaktifkan Mahasiswa' : 'Ya, Aktifkan Kembali'"></span>
                            </button>
                            <button type="button" @click="statusModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Tautan Aktivasi Baru (Flash Session) -->
        @if(session('new_student_activation'))
        @php
            $act = session('new_student_activation');
            $actUrl = $act['url'];
            $actName = $act['name'];
            $actPhone = preg_replace('/[^0-9]/', '', $act['phone'] ?? '');
            $actMsg = "Halo {$actName}, Anda telah didaftarkan pada KASMA (Sistem Kas Mahasiswa TI26A3). Silakan klik tautan berikut untuk membuat kata sandi akun Anda (berlaku 72 jam):\n\n{$actUrl}\n\nTerima kasih!";
        @endphp
        <div x-show="activationModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="activationModalOpen = false">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-xl border border-zinc-200" 
                 @click.away="activationModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <h3 class="font-semibold text-zinc-900 text-sm">Tautan Aktivasi Berhasil Dibuat</h3>
                    </div>
                    <button type="button" @click="activationModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 space-y-3.5 text-xs">
                    <p class="text-zinc-600">
                        Mahasiswa <strong>{{ $actName }}</strong> berhasil didaftarkan. Kirimkan tautan aktivasi berikut:
                    </p>

                    <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg space-y-2">
                        <span class="text-[10px] text-zinc-400 uppercase tracking-wider block font-medium">Tautan Aktivasi Akun (72 Jam)</span>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="{{ $actUrl }}" id="actUrlInput" class="w-full text-xs font-mono bg-white border border-zinc-200 rounded p-1.5 text-zinc-800">
                            <button type="button" 
                                    @click="copyToClipboard('{{ $actUrl }}', false)" 
                                    class="shrink-0 px-2.5 py-1.5 rounded bg-zinc-800 hover:bg-zinc-900 text-white font-medium text-xs transition cursor-pointer">
                                <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                            </button>
                        </div>
                    </div>

                    @if($actPhone)
                    <div class="pt-2">
                        <a href="https://wa.me/{{ $actPhone }}?text={{ urlencode($actMsg) }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-medium text-xs transition cursor-pointer">
                            <span>Kirim Langsung via WhatsApp</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                    @endif

                    <div class="pt-2 border-t border-zinc-100">
                        <button type="button" @click="activationModalOpen = false" class="w-full py-1.5 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Selesai &amp; Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

</x-layouts.app>
