<?php

namespace App\Http\Controllers\Bendahara;

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
     * Display the treasurer dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Current active period
        $activePeriod = CashPeriod::where('is_active', true)->first()
            ?? CashPeriod::orderBy('week_number')->first();

        // 2. Class cash ledger balance and totals
        $currentBalance = FinancialTransaction::currentBalance();
        $totalIncome = (float) FinancialTransaction::income()->sum('amount');
        $totalExpense = (float) FinancialTransaction::expense()->sum('amount');

        // 3. Weekly collection progress for active period
        $totalStudentsCount = User::where('role', 'mahasiswa')->where('is_active', true)->count();
        $activePeriodPaidCount = $activePeriod
            ? StudentDue::where('cash_period_id', $activePeriod->id)->where('status', 'paid')->count()
            : 0;
        $activePeriodTargetAmount = $totalStudentsCount * (float) ($activePeriod?->amount ?? 10000);
        $activePeriodCollectedAmount = $activePeriod
            ? StudentDue::where('cash_period_id', $activePeriod->id)->where('status', 'paid')->sum('amount')
            : 0;
        $activePeriodPercentage = $totalStudentsCount > 0
            ? round(($activePeriodPaidCount / $totalStudentsCount) * 100, 1)
            : 0;

        // 4. Pending payment verification queue
        $pendingPayments = Payment::where('status', 'pending')
            ->with(['studentDue.user', 'studentDue.cashPeriod'])
            ->latest('payment_date')
            ->latest('id')
            ->get();
        $pendingPaymentsCount = $pendingPayments->count();
        $pendingPaymentsAmount = $pendingPayments->sum('amount');

        // 5. Unpaid students for active period
        $unpaidStudents = $activePeriod
            ? StudentDue::where('cash_period_id', $activePeriod->id)
                ->where('status', 'unpaid')
                ->with(['user', 'pendingPayment'])
                ->get()
            : collect();
        $unpaidCount = $unpaidStudents->count();
        $unpaidAmount = $unpaidStudents->sum('amount');

        // 6. Recent ledger transactions
        $transactions = FinancialTransaction::with(['payment.studentDue.user'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(20)
            ->get();

        // 7. Students with unpaid dues for cash payment modal
        $studentsWithUnpaidDues = User::where('role', 'mahasiswa')
            ->where('is_active', true)
            ->whereHas('studentDues', fn($q) => $q->where('status', 'unpaid'))
            ->with(['studentDues' => fn($q) => $q->where('status', 'unpaid')->with('cashPeriod')->orderBy('cash_period_id')])
            ->orderBy('name')
            ->get();

        // 8. All active students for class overview
        $allActiveStudents = User::where('role', 'mahasiswa')
            ->where('is_active', true)
            ->withCount([
                'studentDues as paid_dues_count' => fn($q) => $q->where('status', 'paid'),
                'studentDues as unpaid_dues_count' => fn($q) => $q->where('status', 'unpaid'),
            ])
            ->orderBy('name')
            ->get();

        return view('bendahara.dashboard', compact(
            'user',
            'activePeriod',
            'currentBalance',
            'totalIncome',
            'totalExpense',
            'totalStudentsCount',
            'activePeriodPaidCount',
            'activePeriodTargetAmount',
            'activePeriodCollectedAmount',
            'activePeriodPercentage',
            'pendingPayments',
            'pendingPaymentsCount',
            'pendingPaymentsAmount',
            'unpaidStudents',
            'unpaidCount',
            'unpaidAmount',
            'transactions',
            'studentsWithUnpaidDues',
            'allActiveStudents'
        ));
    }
}
