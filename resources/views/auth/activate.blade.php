<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun Mahasiswa &mdash; KASMA</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="{{ asset('images/logo-udb.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-zinc-900 bg-[#FAF9F5] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        <!-- Logo & Class Header -->
        <div class="text-center">
            <img src="{{ asset('images/logo-udb.png') }}" alt="Logo Universitas Duta Bangsa" class="w-16 h-16 object-contain mx-auto rounded-full shadow-xs">
            <h2 class="mt-4 text-2xl font-bold tracking-tight text-zinc-900">
                Aktivasi Akun Mahasiswa
            </h2>
            <p class="mt-1 text-xs text-zinc-500">
                Sistem Manajemen Kas Kelas TI26A3 &bull; Universitas Duta Bangsa
            </p>
        </div>

        <!-- Card Container -->
        <div class="mt-7 bg-white py-8 px-6 shadow-xs border border-zinc-200 rounded-2xl sm:px-8">
            
            @if(($status ?? 'valid') === 'valid')
                <!-- Student Identity Information -->
                <div class="mb-5 p-3.5 rounded-xl bg-zinc-50 border border-zinc-200 space-y-1">
                    <p class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">Identitas Mahasiswa</p>
                    <p class="text-sm font-bold text-zinc-900">{{ $user->name }}</p>
                    <div class="flex items-center gap-3 text-xs text-zinc-600 font-mono">
                        <span>NIM: {{ $user->nim }}</span>
                        <span>&bull;</span>
                        <span>{{ $user->email }}</span>
                    </div>
                </div>

                <!-- Guidance Box -->
                <p class="text-xs text-zinc-600 mb-5 leading-relaxed">
                    Buat kata sandi akun Anda di bawah ini untuk menyelesaikan aktivasi. Kata sandi ini hanya Anda yang mengetahuinya.
                </p>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('activation.store', ['token' => $token]) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input id="password" 
                                   name="password" 
                                   :type="showPassword ? 'text' : 'password'"
                                   type="password" 
                                   required 
                                   autofocus
                                   autocomplete="new-password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full px-3.5 py-2.5 pr-10 rounded-lg bg-white border border-zinc-300 text-zinc-900 text-sm placeholder-zinc-400 focus:outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 transition">
                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 focus:outline-none cursor-pointer"
                                    :title="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Password Confirmation -->
                    <div x-data="{ showConfirmPassword: false }">
                        <label for="password_confirmation" class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                            Konfirmasi Kata Sandi
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" 
                                   name="password_confirmation" 
                                   :type="showConfirmPassword ? 'text' : 'password'"
                                   type="password" 
                                   required 
                                   autocomplete="new-password"
                                   placeholder="Ulangi kata sandi baru"
                                   class="w-full px-3.5 py-2.5 pr-10 rounded-lg bg-white border border-zinc-300 text-zinc-900 text-sm placeholder-zinc-400 focus:outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 transition">
                            <button type="button" 
                                    @click="showConfirmPassword = !showConfirmPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 focus:outline-none cursor-pointer"
                                    :title="showConfirmPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                                <svg x-show="!showConfirmPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showConfirmPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-2.5 px-4 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-sm transition shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-900 cursor-pointer">
                            Aktifkan Akun &amp; Masuk ke Dashboard
                        </button>
                    </div>
                </form>

            @elseif(($status ?? '') === 'already_activated')
                <!-- Already Activated State -->
                <div class="text-center py-4 space-y-4">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto border border-emerald-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900">Akun Sudah Aktif</h3>
                        <p class="text-xs text-zinc-500 mt-1 leading-relaxed">
                            {{ $message ?? 'Akun Anda sudah diaktifkan sebelumnya. Silakan masuk menggunakan NIM/email dan kata sandi Anda.' }}
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('login') }}" 
                           class="inline-block w-full py-2.5 px-4 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-sm transition shadow-xs text-center">
                            Ke Halaman Masuk
                        </a>
                    </div>
                </div>

            @elseif(($status ?? '') === 'expired')
                <!-- Expired Token State -->
                <div class="text-center py-4 space-y-4">
                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto border border-amber-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900">Tautan Aktivasi Kedaluwarsa</h3>
                        <p class="text-xs text-zinc-500 mt-1 leading-relaxed">
                            {{ $message ?? 'Tautan aktivasi berlaku selama 72 jam dan telah kedaluwarsa. Silakan hubungi bendahara kelas untuk meminta tautan aktivasi baru.' }}
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('login') }}" 
                           class="inline-block w-full py-2.5 px-4 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-sm transition text-center">
                            Kembali ke Halaman Masuk
                        </a>
                    </div>
                </div>

            @elseif(($status ?? '') === 'inactive')
                <!-- Inactive Account State -->
                <div class="text-center py-4 space-y-4">
                    <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900">Akun Dinonaktifkan</h3>
                        <p class="text-xs text-zinc-500 mt-1 leading-relaxed">
                            {{ $message ?? 'Akun Anda saat ini dinonaktifkan oleh bendahara kelas. Silakan hubungi bendahara kelas.' }}
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('login') }}" 
                           class="inline-block w-full py-2.5 px-4 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-sm transition text-center">
                            Kembali ke Halaman Masuk
                        </a>
                    </div>
                </div>

            @else
                <!-- Invalid Token State -->
                <div class="text-center py-4 space-y-4">
                    <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-200">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900">Tautan Aktivasi Tidak Valid</h3>
                        <p class="text-xs text-zinc-500 mt-1 leading-relaxed">
                            {{ $message ?? 'Tautan aktivasi tidak valid atau sudah digantikan dengan tautan yang lebih baru.' }}
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('login') }}" 
                           class="inline-block w-full py-2.5 px-4 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-800 font-semibold text-sm transition text-center">
                            Kembali ke Halaman Masuk
                        </a>
                    </div>
                </div>
            @endif

        </div>

        <!-- Footer -->
        <p class="mt-8 text-center text-xs text-zinc-400">
            &copy; {{ date('Y') }} KASMA &bull; Sistem Kas Kelas Mahasiswa
        </p>
    </div>

</body>
</html>
