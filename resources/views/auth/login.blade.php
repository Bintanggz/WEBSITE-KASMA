<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk &mdash; KASMA (Manajemen Kas Mahasiswa)</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/png" href="{{ asset('images/logo-udb.png') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-stone-800 bg-[#FAF9F5] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    
    <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
        <!-- Logo & Class Header -->
        <div class="text-center">
            <img src="{{ asset('images/logo-udb.png') }}" alt="Logo Universitas Duta Bangsa" class="w-16 h-16 object-contain mx-auto rounded-full shadow-xs">
            <h2 class="mt-4 text-2xl font-bold tracking-tight text-stone-900">
                Masuk ke KASMA
            </h2>
            <p class="mt-1 text-xs text-stone-500">
                Sistem Manajemen Kas Kelas TI26A3 &bull; Universitas Duta Bangsa
            </p>
        </div>

        <!-- Login Card -->
        <div class="mt-8 bg-white py-8 px-6 shadow-xs border border-stone-200/90 rounded-2xl sm:px-8">
            
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
                    <label for="login" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
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
                           class="w-full px-3.5 py-2.5 rounded-lg bg-stone-50/50 border border-stone-300 text-stone-900 text-sm placeholder-stone-400 focus:bg-white focus:outline-none focus:border-stone-800 focus:ring-1 focus:ring-stone-800 transition">
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-stone-700 uppercase tracking-wider">
                            Kata Sandi
                        </label>
                    </div>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-3.5 py-2.5 rounded-lg bg-stone-50/50 border border-stone-300 text-stone-900 text-sm placeholder-stone-400 focus:bg-white focus:outline-none focus:border-stone-800 focus:ring-1 focus:ring-stone-800 transition">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 rounded border-stone-300 text-stone-900 focus:ring-stone-800">
                        <span class="text-xs text-stone-600">Ingat sesi saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-lg bg-stone-900 hover:bg-stone-800 text-white font-semibold text-sm transition shadow-2xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-900">
                        Masuk
                    </button>
                </div>
            </form>

            <!-- Demo Account Credentials Helper -->
            <div class="mt-6 pt-5 border-t border-stone-100">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-400 mb-2.5">Akun Uji Coba (Development)</p>
                <div class="space-y-2 text-xs">
                    <div class="p-2.5 rounded-lg bg-stone-50 border border-stone-200/80 flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-stone-900 block text-[11px]">Bendahara Kelas</span>
                            <span class="font-mono text-stone-500 text-[10px]">bendahara@kasma.edu</span>
                        </div>
                        <span class="text-[10px] font-mono text-stone-600 bg-white border border-stone-200 px-1.5 py-0.5 rounded">pw: password</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-stone-50 border border-stone-200/80 flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-stone-900 block text-[11px]">Mahasiswa (Hafizh)</span>
                            <span class="font-mono text-stone-500 text-[10px]">NIM: 220401 atau hafizh@kasma.edu</span>
                        </div>
                        <span class="text-[10px] font-mono text-stone-600 bg-white border border-stone-200 px-1.5 py-0.5 rounded">pw: password</span>
                    </div>
                </div>
            </div>

        </div>

        <p class="mt-6 text-center text-xs text-stone-400">
            KASMA &bull; Class Cash Management System &bull; Semester Genap 2025/2026
        </p>
    </div>

</body>
</html>
