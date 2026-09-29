<x-layouts.app role="bendahara" title="Kelola Data Mahasiswa">

    <div x-data="{
        addModalOpen: false,
        editModalOpen: false,
        statusModalOpen: false,
        deleteModalOpen: false,
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
        },
        openDelete(student) {
            this.selectedStudent = Object.assign({}, student);
            this.deleteModalOpen = true;
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
                                $isActivated = $student->isActivated();
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
                                        <a href="https://wa.me/{{ $student->whatsapp_number }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer"
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
                                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 transition cursor-pointer"
                                                        title="Kirim atau perbarui tautan aktivasi via WhatsApp">
                                                    <svg class="w-3 h-3 text-emerald-600 fill-current" viewBox="0 0 24 24">
                                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.302c-.087.087-.179.182-.077.357.101.174.453.748.971 1.209.667.593 1.23.777 1.404.864.173.087.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/>
                                                    </svg>
                                                    <span>Kirim Tautan WA</span>
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

                                        <button type="button" 
                                                @click="openDelete({
                                                    id: {{ $student->id }},
                                                    name: '{{ addslashes($student->name) }}'
                                                })"
                                                class="p-1 text-zinc-400 hover:text-rose-600 hover:bg-rose-50 rounded transition cursor-pointer"
                                                title="Hapus Mahasiswa">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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

        <!-- MODAL: Hapus Mahasiswa -->
        <div x-show="deleteModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/40 backdrop-blur-[2px]"
             @keydown.escape.window="deleteModalOpen = false">
            <div class="bg-white rounded-xl max-w-sm w-full p-6 shadow-xl border border-zinc-200" 
                 @click.away="deleteModalOpen = false">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                    <h3 class="font-semibold text-rose-600 text-sm">Hapus Data Mahasiswa</h3>
                    <button type="button" @click="deleteModalOpen = false" class="text-zinc-400 hover:text-zinc-600 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'/bendahara/mahasiswa/' + selectedStudent.id" method="POST" class="mt-4 space-y-3.5 text-xs">
                    @csrf
                    @method('DELETE')
                    
                    <p class="text-zinc-600 leading-relaxed">
                        Apakah Anda yakin ingin menghapus data mahasiswa <strong class="text-zinc-900" x-text="selectedStudent.name"></strong>?
                    </p>

                    <div class="p-3 bg-rose-50 border border-rose-100 rounded-lg text-rose-800 text-[11px] leading-relaxed">
                        Semua data kewajiban iuran dan riwayat pembayaran terkait mahasiswa ini akan dibersihkan. Tindakan ini tidak dapat dibatalkan.
                    </div>

                    <div class="flex gap-2 pt-2 border-t border-zinc-100">
                        <button type="submit" class="flex-1 py-2 px-3 text-xs font-semibold rounded-lg text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer">
                            Ya, Hapus Mahasiswa
                        </button>
                        <button type="button" @click="deleteModalOpen = false" class="py-2 px-3 text-xs font-medium rounded-lg text-zinc-600 bg-zinc-100 hover:bg-zinc-200 transition cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Tautan Aktivasi Baru (Flash Session) -->
        @if(session('new_student_activation'))
        @php
            $act = session('new_student_activation');
            $actUrl = $act['activation_url'] ?? $act['url'] ?? '';
            $actName = $act['name'] ?? '';
            $rawPhone = $act['phone_number'] ?? $act['phone'] ?? '';
            $actPhone = \App\Models\User::formatWhatsappNumber($rawPhone);
            $actMsg = "Halo {$actName},\n\nAkun KASMA Anda telah didaftarkan oleh Bendahara Kelas TI26A3.\nSilakan klik tautan berikut untuk membuat kata sandi dan mengaktifkan akun Anda:\n\n{$actUrl}\n\nTautan ini berlaku selama 72 jam. Terima kasih!";
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
                        Akun mahasiswa <strong>{{ $actName }}</strong> berhasil disiapkan. Silakan kirimkan tautan aktivasi di bawah agar mahasiswa dapat membuat kata sandi:
                    </p>

                    <div class="p-3 bg-zinc-50 border border-zinc-200 rounded-lg space-y-2">
                        <span class="text-[10px] text-zinc-400 uppercase tracking-wider block font-medium">Tautan Aktivasi Akun (72 Jam)</span>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly value="{{ $actUrl }}" id="actUrlInput" class="w-full text-xs font-mono bg-white border border-zinc-200 rounded p-1.5 text-zinc-800">
                            <button type="button" 
                                    @click="copyToClipboard('{{ $actUrl }}', false)" 
                                    class="shrink-0 px-2.5 py-1.5 rounded bg-zinc-800 hover:bg-zinc-900 text-white font-medium text-xs transition cursor-pointer">
                                <span x-text="copied ? 'Tersalin!' : 'Salin Tautan'"></span>
                            </button>
                        </div>
                    </div>

                    @if($actPhone)
                    <div class="space-y-2 pt-1">
                        <a href="https://wa.me/{{ $actPhone }}?text={{ rawurlencode($actMsg) }}" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-xs cursor-pointer">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.302c-.087.087-.179.182-.077.357.101.174.453.748.971 1.209.667.593 1.23.777 1.404.864.173.087.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/>
                            </svg>
                            <span>Buka Chat &amp; Kirim via WhatsApp (+{{ $actPhone }})</span>
                        </a>

                        <button type="button" 
                                @click="copyToClipboard({{ \Illuminate\Support\Js::from($actMsg) }}, true)" 
                                class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 font-medium text-xs transition cursor-pointer">
                            <span x-text="copiedMsg ? 'Teks Pesan Berhasil Tersalin!' : 'Salin Format Pesan Lengkap'"></span>
                        </button>
                    </div>

                    <div class="p-2.5 rounded-lg bg-blue-50 border border-blue-200 text-[11px] text-blue-900 space-y-1">
                        <p class="font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Cara Pengiriman Tautan</span>
                        </p>
                        <p class="text-blue-800 text-[10px] leading-relaxed">
                            KASMA menggunakan tautan langsung (Click-to-Chat) dari WhatsApp Anda ke nomor mahasiswa. Pesan tidak terkirim otomatis di latar belakang tanpa konfirmasi Anda di WhatsApp.
                        </p>
                    </div>
                    @else
                    <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-[11px] text-amber-900">
                        Mahasiswa ini belum memiliki nomor WhatsApp tersimpan. Silakan salin tautan di atas dan kirimkan secara manual.
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
