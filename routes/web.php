<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Bendahara\CashPaymentController;
use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboardController;
use App\Http\Controllers\Bendahara\ExpenseController;
use App\Http\Controllers\Bendahara\PaymentVerificationController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Guest Entry Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return $user->role === 'bendahara'
            ? redirect()->route('bendahara.dashboard')
            : redirect()->route('mahasiswa.dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Common Routes
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Mahasiswa Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('mahasiswa')
    ->name('mahasiswa.')
    ->group(function () {
        Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    });

/*
|--------------------------------------------------------------------------
| Bendahara Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:bendahara'])
    ->prefix('bendahara')
    ->name('bendahara.')
    ->group(function () {
        Route::get('/dashboard', [BendaharaDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/payments/{payment}/approve', [PaymentVerificationController::class, 'approve'])->name('payments.approve');
        Route::patch('/payments/{payment}/reject', [PaymentVerificationController::class, 'reject'])->name('payments.reject');
        Route::post('/cash-payments', [CashPaymentController::class, 'store'])->name('cash-payments.store');
        Route::post('/transactions/expense', [ExpenseController::class, 'store'])->name('transactions.expense.store');
    });
