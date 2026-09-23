<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\CashPeriod;
use App\Models\StudentDue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentDueController extends Controller
{
    /**
     * Display a listing of weekly dues obligations for the authenticated student.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Current active period and student's obligation
        $activePeriod = CashPeriod::where('is_active', true)->first();
        $currentDue = $activePeriod
            ? StudentDue::where('cash_period_id', $activePeriod->id)
                ->where('user_id', $user->id)
                ->first()
            : null;

        // 2. All weekly obligations for this student
        $studentDues = StudentDue::where('user_id', $user->id)
            ->with(['cashPeriod', 'pendingPayment', 'rejectedPayment'])
            ->get()
            ->sortBy(fn($d) => [
                $d->cashPeriod->academic_year ?? '',
                $d->cashPeriod->semester ?? '',
                $d->cashPeriod->week_number ?? 0,
            ]);

        // 3. Summary metrics
        $paidDuesCount = $studentDues->where('status', 'paid')->count();
        $unpaidDuesCount = $studentDues->where('status', 'unpaid')->count();
        $totalDuesCount = $studentDues->count();

        $totalPaidAmount = $studentDues->where('status', 'paid')->sum('amount');
        $totalUnpaidAmount = $studentDues->where('status', 'unpaid')->sum('amount');
        $totalObligationAmount = $studentDues->sum('amount');

        $completionPercentage = $totalDuesCount > 0
            ? round(($paidDuesCount / $totalDuesCount) * 100, 1)
            : 0;

        return view('mahasiswa.iuran.index', compact(
            'user',
            'activePeriod',
            'currentDue',
            'studentDues',
            'paidDuesCount',
            'unpaidDuesCount',
            'totalDuesCount',
            'totalPaidAmount',
            'totalUnpaidAmount',
            'totalObligationAmount',
            'completionPercentage'
        ));
    }
}
