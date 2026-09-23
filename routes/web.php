<?php

use App\Http\Controllers\Auth\AccountActivationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Bendahara\CashPaymentController;
use App\Http\Controllers\Bendahara\CashPeriodController;
use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboardController;
use App\Http\Controllers\Bendahara\ExpenseController;
use App\Http\Controllers\Bendahara\FinancialTransactionController;
use App\Http\Controllers\Bendahara\PaymentVerificationController;
use App\Http\Controllers\Bendahara\ReportController;
use App\Http\Controllers\Bendahara\StudentManagementController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\FinancialTransparencyController;
use App\Http\Controllers\Mahasiswa\PaymentController;
use App\Http\Controllers\Mahasiswa\StudentDueController;
use App\Http\Controllers\PaymentProofController;
use App\Http\Controllers\TransactionReceiptController;
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

// Student Self-Activation Routes (Public with token verification)
Route::get('/aktivasi/{token}', [AccountActivationController::class, 'show'])->name('activation.show');
Route::post('/aktivasi/{token}', [AccountActivationController::class, 'activate'])->name('activation.store');

/*
|--------------------------------------------------------------------------
| Authenticated Common Routes
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/payments/{payment}/proof', [PaymentProofController::class, 'show'])
    ->middleware('auth')
    ->name('payments.proof');

Route::get('/transactions/{transaction}/receipt', [TransactionReceiptController::class, 'show'])
    ->middleware('auth')
    ->name('transactions.receipt');

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
        Route::get('/iuran', [StudentDueController::class, 'index'])->name('iuran.index');
        Route::get('/riwayat', [PaymentController::class, 'history'])->name('riwayat.index');
        Route::get('/keuangan', [FinancialTransparencyController::class, 'index'])->name('keuangan.index');
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
        
        // Iuran Kas / Weekly Cash Periods
        Route::get('/iuran', [CashPeriodController::class, 'index'])->name('iuran.index');
        Route::post('/iuran', [CashPeriodController::class, 'store'])->name('iuran.store');
        Route::get('/iuran/{period}', [CashPeriodController::class, 'show'])->name('iuran.show');
        Route::patch('/iuran/{period}/activate', [CashPeriodController::class, 'activate'])->name('iuran.activate');
        Route::patch('/iuran/{period}/deactivate', [CashPeriodController::class, 'deactivate'])->name('iuran.deactivate');

        // Payment Verification
        Route::get('/verifikasi', [PaymentVerificationController::class, 'index'])->name('verifikasi.index');
        Route::patch('/payments/{payment}/approve', [PaymentVerificationController::class, 'approve'])->name('payments.approve');
        Route::patch('/payments/{payment}/reject', [PaymentVerificationController::class, 'reject'])->name('payments.reject');
        Route::post('/cash-payments', [CashPaymentController::class, 'store'])->name('cash-payments.store');

        // Financial Transactions & Ledger
        Route::get('/transaksi', [FinancialTransactionController::class, 'index'])->name('transaksi.index');
        Route::post('/transaksi', [FinancialTransactionController::class, 'store'])->name('transaksi.store');
        Route::put('/transaksi/{transaction}', [FinancialTransactionController::class, 'update'])->name('transaksi.update');
        Route::delete('/transaksi/{transaction}', [FinancialTransactionController::class, 'destroy'])->name('transaksi.destroy');
        Route::post('/transactions/expense', [ExpenseController::class, 'store'])->name('transactions.expense.store');

        // Student Management / Data Mahasiswa
        Route::get('/mahasiswa', [StudentManagementController::class, 'index'])->name('mahasiswa.index');
        Route::post('/mahasiswa', [StudentManagementController::class, 'store'])->name('mahasiswa.store');
        Route::put('/mahasiswa/{mahasiswa}', [StudentManagementController::class, 'update'])->name('mahasiswa.update');
        Route::patch('/mahasiswa/{mahasiswa}/toggle-status', [StudentManagementController::class, 'toggleStatus'])->name('mahasiswa.toggle-status');
        Route::post('/mahasiswa/{mahasiswa}/resend-activation', [StudentManagementController::class, 'resendActivation'])->name('mahasiswa.resend-activation');

        // Reports / Laporan
        Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    });
