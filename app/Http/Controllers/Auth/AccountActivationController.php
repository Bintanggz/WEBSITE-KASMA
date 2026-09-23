<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ActivateAccountRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountActivationController extends Controller
{
    /**
     * Show the account activation form.
     */
    public function show(Request $request, string $token): View
    {
        $email = $request->query('email');

        if (! $email) {
            return view('auth.activate', [
                'status' => 'invalid',
                'message' => 'Tautan aktivasi tidak valid atau alamat email tidak disertakan.',
            ]);
        }

        $user = User::where('email', $email)->where('role', 'mahasiswa')->first();

        if (! $user) {
            return view('auth.activate', [
                'status' => 'invalid',
                'message' => 'Akun mahasiswa dengan alamat email ini tidak ditemukan.',
            ]);
        }

        if ($user->isActivated()) {
            return view('auth.activate', [
                'status' => 'already_activated',
                'message' => 'Akun Anda sudah diaktifkan sebelumnya. Silakan masuk menggunakan kata sandi Anda.',
                'user' => $user,
            ]);
        }

        $hashedToken = hash('sha256', $token);
        if (! hash_equals($user->activation_token ?? '', $hashedToken)) {
            return view('auth.activate', [
                'status' => 'invalid',
                'message' => 'Tautan aktivasi tidak valid atau sudah tidak berlaku lagi.',
                'user' => $user,
            ]);
        }

        if ($user->isActivationExpired()) {
            return view('auth.activate', [
                'status' => 'expired',
                'message' => 'Tautan aktivasi telah kedaluwarsa (berlaku maksimal 72 jam). Silakan hubungi bendahara kelas untuk meminta tautan aktivasi baru.',
                'user' => $user,
            ]);
        }

        if (! $user->is_active) {
            return view('auth.activate', [
                'status' => 'inactive',
                'message' => 'Akun ini dinonaktifkan oleh bendahara kelas. Silakan hubungi bendahara untuk mengaktifkannya kembali.',
                'user' => $user,
            ]);
        }

        return view('auth.activate', [
            'status' => 'valid',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Handle the submission of password to activate the student account.
     */
    public function activate(ActivateAccountRequest $request, string $token): RedirectResponse
    {
        $user = User::where('email', $request->validated('email'))
            ->where('role', 'mahasiswa')
            ->first();

        if (! $user) {
            return redirect()->back()
                ->withErrors(['email' => 'Akun mahasiswa tidak ditemukan.'])
                ->withInput();
        }

        if ($user->isActivated()) {
            return redirect()->route('login')
                ->with('status', 'Akun Anda sudah diaktifkan sebelumnya. Silakan masuk.');
        }

        $hashedToken = hash('sha256', $token);
        if (! hash_equals($user->activation_token ?? '', $hashedToken)) {
            return redirect()->back()
                ->withErrors(['password' => 'Tautan aktivasi tidak valid atau telah diganti.'])
                ->withInput();
        }

        if ($user->isActivationExpired()) {
            return redirect()->back()
                ->withErrors(['password' => 'Tautan aktivasi telah kedaluwarsa. Silakan hubungi bendahara kelas untuk meminta tautan baru.'])
                ->withInput();
        }

        if (! $user->is_active) {
            return redirect()->back()
                ->withErrors(['password' => 'Akun Anda dinonaktifkan oleh bendahara kelas.'])
                ->withInput();
        }

        // Activate user and set password inside a transaction
        DB::transaction(function () use ($user, $request) {
            $user->update([
                'password' => Hash::make($request->validated('password')),
                'activated_at' => now(),
                'activation_token' => null,
                'activation_expires_at' => null,
            ]);
        });

        // Automatically log in the user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', 'Selamat datang! Akun KASMA Anda berhasil diaktifkan.');
    }
}
