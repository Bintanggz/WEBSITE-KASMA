<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk &mdash; KASMA (Manajemen Kas Mahasiswa)</title>
    
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
                Masuk ke KASMA
            </h2>
            <p class="mt-1 text-xs text-zinc-500">
                Sistem Manajemen Kas Kelas TI26A3 &bull; Universitas Duta Bangsa
            </p>
        </div>

        <!-- Login Card -->
        <div class="mt-7 bg-white py-8 px-6 shadow-xs border border-zinc-200 rounded-2xl sm:px-8">
            
            <!-- Global / Session Error Message -->
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

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Login (Email or NIM) -->
                <div>
                    <label for="login" class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                        Email atau NIM Mahasiswa
                    </label>
                    <input id="login" 
                           name="login" 
                           type="text" 
                           value="{{ old('login') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="nama@kasma.edu atau 220401"
                           class="w-full px-3.5 py-2.5 rounded-lg bg-white border border-zinc-300 text-zinc-900 text-sm placeholder-zinc-400 focus:outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 transition">
                </div>

                <!-- Password with Show/Hide Toggle -->
                <div x-data="{ showPassword: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                    </div>
                    <div class="relative">
                        <input id="password" 
                               name="password" 
                               :type="showPassword ? 'text' : 'password'"
                               type="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full px-3.5 py-2.5 pr-10 rounded-lg bg-white border border-zinc-300 text-zinc-900 text-sm placeholder-zinc-400 focus:outline-none focus:border-zinc-900 focus:ring-1 focus:ring-zinc-900 transition">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 focus:outline-none cursor-pointer"
                                :title="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                            <!-- Eye icon (when hidden) -->
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash icon (when shown) -->
                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900">
                        <span class="text-xs text-zinc-600">Ingat sesi saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-white font-semibold text-sm transition shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-900 cursor-pointer">
                        Masuk
                    </button>
                </div>
            </form>

            <!-- Demo Account Credentials Helper -->
            @if (app()->environment('local', 'testing'))
                <div class="mt-6 pt-5 border-t border-zinc-100">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400 mb-2.5">Akun Uji Coba (Klik untuk Isi Otomatis)</p>
                    <div class="space-y-2 text-xs">
                        <div role="button"
                             onclick="document.getElementById('login').value = 'bendahara@kasma.edu'; document.getElementById('password').value = 'password';"
                             class="p-2.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 flex items-center justify-between cursor-pointer transition">
                            <div>
                                <span class="font-semibold text-zinc-900 block text-[11px]">Bendahara Kelas</span>
                                <span class="font-mono text-zinc-500 text-[10px]">bendahara@kasma.edu</span>
                            </div>
                            <span class="text-[10px] font-mono text-zinc-600 bg-white border border-zinc-200 px-1.5 py-0.5 rounded shadow-2xs">Gunakan &rarr;</span>
                        </div>
                        <div role="button"
                             onclick="document.getElementById('login').value = '220401'; document.getElementById('password').value = 'password';"
                             class="p-2.5 rounded-lg bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 flex items-center justify-between cursor-pointer transition">
                            <div>
                                <span class="font-semibold text-zinc-900 block text-[11px]">Mahasiswa (Hafizh)</span>
                                <span class="font-mono text-zinc-500 text-[10px]">NIM: 220401 atau hafizh@kasma.edu</span>
                            </div>
                            <span class="text-[10px] font-mono text-zinc-600 bg-white border border-zinc-200 px-1.5 py-0.5 rounded shadow-2xs">Gunakan &rarr;</span>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <p class="mt-6 text-center text-xs text-zinc-400">
            KASMA &bull; Class Cash Management System &bull; Semester Genap 2025/2026
        </p>
    </div>

</body>
</html>
