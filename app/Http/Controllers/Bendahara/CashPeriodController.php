<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\StoreCashPeriodSetRequest;
use App\Models\CashPeriod;
use App\Models\User;
use App\Services\CashPeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashPeriodController extends Controller
{
    /**
     * Display a listing of weekly cash periods.
     */
    public function index(Request $request): View
    {
        $activePeriod = CashPeriod::where('is_active', true)->first();
        $totalStudents = User::where('role', 'mahasiswa')->where('is_active', true)->count();

        $periods = CashPeriod::withCount([
            'studentDues as total_dues_count',
            'studentDues as paid_dues_count' => fn($q) => $q->where('status', 'paid'),
            'studentDues as unpaid_dues_count' => fn($q) => $q->where('status', 'unpaid'),
        ])
        ->withSum([
            'studentDues as paid_amount' => fn($q) => $q->where('status', 'paid'),
        ], 'amount')
        ->orderBy('academic_year', 'desc')
        ->orderBy('semester', 'desc')
        ->orderBy('week_number', 'asc')
        ->get();

        // Group by academic year & semester
        $groupedPeriods = $periods->groupBy(fn($p) => $p->academic_year . ' - Semester ' . ucfirst($p->semester));

        $totalPaidDuesAll = $periods->sum('paid_dues_count');
        $totalDuesAll = $periods->sum('total_dues_count');
        $overallPercentage = $totalDuesAll > 0 ? round(($totalPaidDuesAll / $totalDuesAll) * 100, 1) : 0;

        return view('bendahara.iuran.index', compact(
            'periods',
            'groupedPeriods',
            'activePeriod',
            'totalStudents',
            'overallPercentage'
        ));
    }

    /**
     * Store a newly created semester cash period set.
     */
    public function store(StoreCashPeriodSetRequest $request, CashPeriodService $service): RedirectResponse
    {
        $created = $service->createSemesterSet($request->validated());

        return redirect()->route('bendahara.iuran.index')->with(
            'success',
            "Berhasil membuat {$created->count()} pekan periode kas semester dan mengalokasikan kewajiban kas untuk mahasiswa aktif!"
        );
    }

    /**
     * Display the specified cash period and student payment status.
     */
    public function show(CashPeriod $period): View
    {
        $period->loadCount([
            'studentDues as total_dues_count',
            'studentDues as paid_dues_count' => fn($q) => $q->where('status', 'paid'),
            'studentDues as unpaid_dues_count' => fn($q) => $q->where('status', 'unpaid'),
        ]);

        $dues = $period->studentDues()
            ->with(['user'])
            ->get()
            ->sortBy(fn($d) => $d->user->name ?? '');

        $totalStudents = $period->total_dues_count;
        $paidCount = $period->paid_dues_count;
        $unpaidCount = $period->unpaid_dues_count;
        $paidAmount = $dues->where('status', 'paid')->sum('amount');
        $targetAmount = $totalStudents * (float) $period->amount;
        $percentage = $totalStudents > 0 ? round(($paidCount / $totalStudents) * 100, 1) : 0;

        return view('bendahara.iuran.show', compact(
            'period',
            'dues',
            'totalStudents',
            'paidCount',
            'unpaidCount',
            'paidAmount',
            'targetAmount',
            'percentage'
        ));
    }

    /**
     * Activate a cash period.
     */
    public function activate(CashPeriod $period, CashPeriodService $service): RedirectResponse
    {
        $service->activatePeriod($period);

        return redirect()->back()->with(
            'success',
            "{$period->name} berhasil diaktifkan sebagai pekan kas berjalan."
        );
    }

    /**
     * Deactivate a cash period.
     */
    public function deactivate(CashPeriod $period, CashPeriodService $service): RedirectResponse
    {
        $service->deactivatePeriod($period);

        return redirect()->back()->with(
            'success',
            "{$period->name} telah dinonaktifkan."
        );
    }
}
