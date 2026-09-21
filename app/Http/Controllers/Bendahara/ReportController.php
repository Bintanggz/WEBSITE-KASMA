<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\CashPeriod;
use App\Models\FinancialTransaction;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the Treasurer Reports and Financial Summary page.
     */
    public function index(Request $request): View
    {
        // 1. Overall Ledger Financial Summary (Source of Truth)
        $totalIncome = (float) FinancialTransaction::income()->sum('amount');
        $totalExpense = (float) FinancialTransaction::expense()->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        // 2. All Cash Periods for filter and rekapitulasi
        $cashPeriods = CashPeriod::orderBy('week_number')->get();
        $totalActiveStudents = User::where('role', 'mahasiswa')->where('is_active', true)->count();

        // 3. Weekly Collection Summary
        $weeklySummaries = $cashPeriods->map(function ($period) use ($totalActiveStudents) {
            $paidCount = StudentDue::where('cash_period_id', $period->id)->where('status', 'paid')->count();
            $unpaidCount = StudentDue::where('cash_period_id', $period->id)->where('status', 'unpaid')->count();
            $collectedAmount = (float) StudentDue::where('cash_period_id', $period->id)->where('status', 'paid')->sum('amount');
            $targetAmount = (float) $period->amount * $totalActiveStudents;
            $percentage = $totalActiveStudents > 0 ? round(($paidCount / $totalActiveStudents) * 100, 1) : 0;

            return (object) [
                'period' => $period,
                'paid_count' => $paidCount,
                'unpaid_count' => $unpaidCount,
                'collected_amount' => $collectedAmount,
                'target_amount' => $targetAmount,
                'percentage' => $percentage,
            ];
        });

        // 4. Student Compliance Summary
        $studentSummaries = User::where('role', 'mahasiswa')
            ->where('is_active', true)
            ->with(['studentDues'])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $paidDues = $student->studentDues->where('status', 'paid');
                $unpaidDues = $student->studentDues->where('status', 'unpaid');

                return (object) [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nim' => $student->nim,
                    'phone_number' => $student->phone_number,
                    'paid_weeks_count' => $paidDues->count(),
                    'unpaid_weeks_count' => $unpaidDues->count(),
                    'total_paid_amount' => (float) $paidDues->sum('amount'),
                    'total_unpaid_amount' => (float) $unpaidDues->sum('amount'),
                ];
            });

        // 5. Transaction History Query with Filters
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type', 'all');
        $cashPeriodId = $request->query('cash_period_id', 'all');

        $transactionQuery = FinancialTransaction::with([
            'creator',
            'payment.studentDue.user',
            'payment.studentDue.cashPeriod',
        ]);

        if (in_array($type, ['income', 'expense'])) {
            $transactionQuery->where('type', $type);
        }

        if ($startDate) {
            $transactionQuery->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $transactionQuery->whereDate('transaction_date', '<=', $endDate);
        }

        if ($cashPeriodId && $cashPeriodId !== 'all') {
            $transactionQuery->whereHas('payment.studentDue', function ($q) use ($cashPeriodId) {
                $q->where('cash_period_id', $cashPeriodId);
            });
        }

        $transactions = $transactionQuery->latest('transaction_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('bendahara.laporan.index', compact(
            'totalIncome',
            'totalExpense',
            'currentBalance',
            'cashPeriods',
            'totalActiveStudents',
            'weeklySummaries',
            'studentSummaries',
            'transactions',
            'startDate',
            'endDate',
            'type',
            'cashPeriodId'
        ));
    }
}
