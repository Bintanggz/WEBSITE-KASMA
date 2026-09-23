<x-layouts.app :role="$user->role" title="Pengaturan Akun & Profil">

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header -->
        <div class="pb-4 border-b border-stone-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-xl font-bold text-stone-900 tracking-tight">Pengaturan Akun &amp; Profil</h2>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Kelola informasi kontak WhatsApp, identitas akun, dan keamanan kata sandi Anda.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold {{ $user->isBendahara() ? 'bg-amber-50 text-amber-900 border border-amber-200/80' : 'bg-emerald-50 text-emerald-900 border border-emerald-200/80' }}">
                        <span class="w-2 h-2 rounded-full {{ $user->isBendahara() ? 'bg-amber-600' : 'bg-emerald-600' }}"></span>
                        <span>{{ $user->isBendahara() ? 'Bendahara Kas Kelas' : 'Mahasiswa Kelas TI26A3' }}</span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Feedback Alert Messages -->
        @if(session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                <div class="flex items-center gap-2 font-semibold">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Terjadi kesalahan pada data yang Anda masukkan:</span>
                </div>
                <ul class="list-disc list-inside pl-6 text-[11px] space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card 1: Informasi Identitas & Kontak -->
            <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs p-5 sm:p-6 space-y-5">
                <div class="border-b border-stone-100 pb-3">
                    <h3 class="text-sm font-bold text-stone-900">Identitas &amp; Kontak</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Data identitas mahasiswa dan nomor WhatsApp aktif</p>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div>
                        <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block mb-1">Nama Lengkap</span>
                        <div class="p-2.5 rounded-lg bg-stone-50 border border-stone-200 text-stone-800 font-semibold">
                            {{ $user->name }}
                        </div>
                    </div>

                    @if($user->nim)
                    <div>
                        <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block mb-1">Nomor Induk Mahasiswa (NIM)</span>
                        <div class="p-2.5 rounded-lg bg-stone-50 border border-stone-200 font-mono text-stone-800">
                            {{ $user->nim }}
                        </div>
                    </div>
                    @endif

                    <div>
                        <span class="text-[11px] font-semibold text-stone-400 uppercase tracking-wider block mb-1">Alamat Email</span>
                        <div class="p-2.5 rounded-lg bg-stone-50 border border-stone-200 font-mono text-stone-800">
                            {{ $user->email }}
                        </div>
                    </div>

                    <!-- Update Phone Number Form -->
                    <form method="POST" action="{{ route('profile.update') }}" class="pt-2 border-t border-stone-100 space-y-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="phone_number" class="text-[11px] font-semibold text-stone-700 uppercase tracking-wider block mb-1">
                                Nomor WhatsApp / Telepon
                            </label>
                            <div class="relative">
                                <input type="text"
                                       id="phone_number"
                                       name="phone_number"
                                       value="{{ old('phone_number', $user->phone_number) }}"
                                       placeholder="Contoh: 081234567890"
                                       class="w-full px-3.5 py-2 text-xs rounded-lg border border-stone-300 bg-white text-stone-900 placeholder-stone-400 focus:outline-none focus:border-stone-800 focus:ring-1 focus:ring-stone-800 transition">
                            </div>
                            <p class="text-[10px] text-stone-500 mt-1">
                                Digunakan untuk konfirmasi setoran kas dan pengingat tagihan via WhatsApp.
                            </p>
                        </div>

                        <div class="pt-1">
                            <button type="submit"
                                    class="px-4 py-2 text-xs font-semibold rounded-lg bg-stone-900 hover:bg-stone-800 text-white transition shadow-2xs cursor-pointer">
                                Simpan Perubahan Kontak
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card 2: Ubah Kata Sandi Akun -->
            <div class="bg-white rounded-xl border border-stone-200/90 shadow-2xs p-5 sm:p-6 space-y-5">
                <div class="border-b border-stone-100 pb-3">
                    <h3 class="text-sm font-bold text-stone-900">Keamanan &amp; Kata Sandi</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Perbarui kata sandi Anda secara berkala untuk menjaga keamanan akun</p>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div x-data="{ showCurrent: false }">
                        <label for="current_password" class="text-[11px] font-semibold text-stone-700 uppercase tracking-wider block mb-1">
                            Kata Sandi Saat Ini
                        </label>
                        <div class="relative">
                            <input id="current_password"
                                   name="current_password"
                                   :type="showCurrent ? 'text' : 'password'"
                                   type="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="Masukkan kata sandi lama Anda"
                                   class="w-full px-3.5 py-2 pr-10 text-xs rounded-lg border border-stone-300 bg-white text-stone-900 placeholder-stone-400 focus:outline-none focus:border-stone-800 focus:ring-1 focus:ring-stone-800 transition">
                            <button type="button"
                                    @click="showCurrent = !showCurrent"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 focus:outline-none cursor-pointer"
                                    :title="showCurrent ? 'Sembunyikan' : 'Lihat'">
                                <svg x-show="!showCurrent" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showCurrent" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div x-data="{ showNew: false }">
                        <label for="password" class="text-[11px] font-semibold text-stone-700 uppercase tracking-wider block mb-1">
                            Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input id="password"
                                   name="password"
                                   :type="showNew ? 'text' : 'password'"
                                   type="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-3.5 py-2 pr-10 text-xs rounded-lg border border-stone-300 bg-white text-stone-900 placeholder-stone-400 focus:outline-none focus:border-stone-800 focus:ring-1 focus:ring-stone-800 transition">
                            <button type="button"
                                    @click="showNew = !showNew"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 focus:outline-none cursor-pointer"
                                    :title="showNew ? 'Sembunyikan' : 'Lihat'">
                                <svg x-show="!showNew" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showNew" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Password Confirmation -->
                    <div x-data="{ showConfirm: false }">
                        <label for="password_confirmation" class="text-[11px] font-semibold text-stone-700 uppercase tracking-wider block mb-1">
                            Ulangi Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input id="password_confirmation"
                                   name="password_confirmation"
                                   :type="showConfirm ? 'text' : 'password'"
                                   type="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Ketik ulang kata sandi baru"
                                   class="w-full px-3.5 py-2 pr-10 text-xs rounded-lg border border-stone-300 bg-white text-stone-900 placeholder-stone-400 focus:outline-none focus:border-stone-800 focus:ring-1 focus:ring-stone-800 transition">
                            <button type="button"
                                    @click="showConfirm = !showConfirm"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600 focus:outline-none cursor-pointer"
                                    :title="showConfirm ? 'Sembunyikan' : 'Lihat'">
                                <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="w-full py-2.5 px-4 text-xs font-semibold rounded-lg bg-stone-900 hover:bg-stone-800 text-white transition shadow-2xs cursor-pointer">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</x-layouts.app>
