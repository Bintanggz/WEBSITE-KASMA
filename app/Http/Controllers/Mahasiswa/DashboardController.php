<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\CashPeriod;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Current active period and student's obligation
        $activePeriod = CashPeriod::where('is_active', true)->first()
            ?? CashPeriod::orderBy('week_number')->first();

        $currentDue = $activePeriod
            ? StudentDue::where('cash_period_id', $activePeriod->id)
                ->where('user_id', $user->id)
                ->with(['approvedPayment', 'pendingPayment'])
                ->first()
            : null;

        // 2. Unpaid obligations available for payment
        $unpaidDues = StudentDue::where('user_id', $user->id)
            ->where('status', 'unpaid')
            ->with(['cashPeriod', 'pendingPayment'])
            ->get()
            ->sortBy(fn($d) => $d->cashPeriod->week_number ?? 0);

        $unpaidAmount = $unpaidDues->sum('amount');

        // 3. Semester progress and weekly matrix
        $allPeriods = CashPeriod::orderBy('week_number')->get();
        $studentDuesMap = StudentDue::where('user_id', $user->id)
            ->with(['approvedPayment', 'pendingPayment'])
            ->get()
            ->keyBy('cash_period_id');

        $paidWeeksCount = StudentDue::where('user_id', $user->id)
            ->where('status', 'paid')
            ->count();
        $totalWeeksCount = max($allPeriods->count(), 16);
        $progressPercent = $totalWeeksCount > 0 ? round(($paidWeeksCount / $totalWeeksCount) * 100, 1) : 0;

        $totalPaidAmount = Payment::whereHas('studentDue', fn($q) => $q->where('user_id', $user->id))
            ->where('status', 'approved')
            ->sum('amount');

        // 4. Payment history
        $payments = Payment::whereHas('studentDue', fn($q) => $q->where('user_id', $user->id))
            ->with(['studentDue.cashPeriod'])
            ->latest('payment_date')
            ->latest('id')
            ->take(15)
            ->get();

        // 5. Class financial transparency
        $currentBalance = FinancialTransaction::currentBalance();
        $totalIncome = FinancialTransaction::income()->sum('amount');
        $totalExpense = FinancialTransaction::expense()->sum('amount');
        $recentExpenses = FinancialTransaction::expense()
            ->latest('transaction_date')
            ->latest('id')
            ->take(3)
            ->get();

        // 6. Bendahara contact
        $bendaharaContact = User::where('role', 'bendahara')
            ->where('is_active', true)
            ->first();

        return view('mahasiswa.dashboard', compact(
            'user',
            'activePeriod',
            'currentDue',
            'unpaidDues',
            'unpaidAmount',
            'allPeriods',
            'studentDuesMap',
            'paidWeeksCount',
            'totalWeeksCount',
            'progressPercent',
            'totalPaidAmount',
            'payments',
            'currentBalance',
            'totalIncome',
            'totalExpense',
            'recentExpenses',
            'bendaharaContact'
        ));
    }
}
