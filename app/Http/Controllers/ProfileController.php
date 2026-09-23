<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's account and security settings.
     */
    public function edit(): View
    {
        $user = auth()->user();

        $stats = [];
        if ($user->isMahasiswa()) {
            $paidWeeksCount = $user->studentDues()->where('status', 'paid')->count();
            $unpaidWeeksCount = $user->studentDues()->where('status', 'unpaid')->count();
            $totalPaidAmount = (float) $user->studentDues()->where('status', 'paid')->sum('amount');
            $stats = compact('paidWeeksCount', 'unpaidWeeksCount', 'totalPaidAmount');
        } else {
            $verifiedCount = $user->verifiedPayments()->where('status', 'approved')->count();
            $transactionsCount = $user->financialTransactions()->count();
            $stats = compact('verifiedCount', 'transactionsCount');
        }

        return view('profile.edit', compact('user', 'stats'));
    }

    /**
     * Update the user's contact profile information.
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $user->update([
            'phone_number' => $request->validated('phone_number'),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Nomor WhatsApp / kontak berhasil diperbarui.');
    }

    /**
     * Update the user's account password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Kata sandi akun Anda berhasil diperbarui.');
    }
}
