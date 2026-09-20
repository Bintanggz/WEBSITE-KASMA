<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mahasiswa\StorePaymentRequest;
use App\Models\Payment;
use App\Models\StudentDue;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Store student payment submission.
     */
    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $dueIds = $request->input('student_due_ids', []);
        $method = $request->input('payment_method');
        $file = $request->file('proof_file');

        // Store proof image on private storage disk
        $path = $file->store('proofs', 'local');

        $now = Carbon::now();

        DB::transaction(function () use ($dueIds, $method, $path, $now) {
            $dues = StudentDue::whereIn('id', $dueIds)->lockForUpdate()->get();

            foreach ($dues as $due) {
                Payment::create([
                    'student_due_id' => $due->id,
                    'amount' => $due->amount,
                    'payment_method' => $method,
                    'proof_file_path' => $path,
                    'payment_date' => $now,
                    'status' => 'pending',
                ]);
            }
        });

        $weeksCount = count($dueIds);
        $message = $weeksCount > 1
            ? "Bukti pembayaran untuk {$weeksCount} pekan berhasil dikirim! Menunggu verifikasi bendahara."
            : 'Bukti pembayaran berhasil dikirim! Menunggu verifikasi bendahara.';

        return redirect()->route('mahasiswa.dashboard')->with('success', $message);
    }

    /**
     * Display student's payment history and status tracking.
     */
    public function history()
    {
        $user = auth()->user();

        $payments = Payment::whereHas('studentDue', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['studentDue.cashPeriod', 'verifier'])
        ->latest('payment_date')
        ->latest('id')
        ->get();

        $totalApprovedAmount = $payments->where('status', 'approved')->sum('amount');
        $pendingCount = $payments->where('status', 'pending')->count();
        $approvedCount = $payments->where('status', 'approved')->count();
        $rejectedCount = $payments->where('status', 'rejected')->count();

        $unpaidDues = $user->studentDues()
            ->where('status', 'unpaid')
            ->with(['cashPeriod', 'pendingPayment'])
            ->get()
            ->sortBy(fn ($d) => $d->cashPeriod->week_number ?? 0);

        return view('mahasiswa.riwayat.index', compact(
            'user',
            'payments',
            'totalApprovedAmount',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'unpaidDues'
        ));
    }
}
