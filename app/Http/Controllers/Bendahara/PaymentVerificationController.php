<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\RejectPaymentRequest;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentVerificationController extends Controller
{
    /**
     * Display the payment verification dashboard with filterable tabs.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');
        if (! in_array($status, ['pending', 'approved', 'rejected', 'all'])) {
            $status = 'pending';
        }

        $query = Payment::with(['studentDue.user', 'studentDue.cashPeriod', 'verifier']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $payments = $query->latest('payment_date')->latest('id')->paginate(15)->withQueryString();

        $pendingCount = Payment::where('status', 'pending')->count();
        $approvedCount = Payment::where('status', 'approved')->count();
        $rejectedCount = Payment::where('status', 'rejected')->count();
        $totalCount = Payment::count();
        $totalIncome = Payment::where('status', 'approved')->sum('amount');

        $unpaidDues = StudentDue::where('status', 'unpaid')
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->with(['user', 'cashPeriod'])
            ->get()
            ->sortBy(fn ($d) => [$d->user->name ?? '', $d->cashPeriod->week_number ?? 0]);

        return view('bendahara.verifikasi.index', compact(
            'payments',
            'status',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalCount',
            'totalIncome',
            'unpaidDues'
        ));
    }

    /**
     * Approve a pending student payment.
     */
    public function approve(Payment $payment): RedirectResponse
    {
        if (! $payment->isPending()) {
            return redirect()->back()->with('error', 'Pembayaran ini sudah tidak dalam status menunggu verifikasi.');
        }

        try {
            DB::transaction(function () use ($payment) {
                $payment->refresh();
                $due = $payment->studentDue()->lockForUpdate()->first();

                if ($due->isPaid()) {
                    throw new \Exception('Kewajiban kas pekan ini sudah tercatat lunas sebelumnya.');
                }

                $now = Carbon::now();

                $payment->update([
                    'status' => 'approved',
                    'verified_by' => Auth::id(),
                    'verified_at' => $now,
                ]);

                $due->update([
                    'status' => 'paid',
                ]);

                // Create ledger income entry
                FinancialTransaction::create([
                    'type' => 'income',
                    'amount' => $payment->amount,
                    'transaction_date' => $now->toDateString(),
                    'category' => 'Iuran Kas',
                    'description' => 'Iuran ' . ($due->cashPeriod->name ?? 'Kas') . ' - ' . $due->user->name . ' (' . $due->user->nim . ')',
                    'payment_id' => $payment->id,
                    'receipt_path' => $payment->proof_file_path,
                    'created_by' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui pembayaran: ' . $e->getMessage());
        }

        $studentName = $payment->studentDue?->user?->name ?? 'Mahasiswa';

        return redirect()->back()->with('success', "Pembayaran dari {$studentName} berhasil disetujui dan kas telah bertambah!");
    }

    /**
     * Reject a pending student payment.
     */
    public function reject(RejectPaymentRequest $request, Payment $payment): RedirectResponse
    {
        if (! $payment->isPending()) {
            return redirect()->back()->with('error', 'Pembayaran ini sudah tidak dalam status menunggu verifikasi.');
        }

        $now = Carbon::now();

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $request->validated('rejection_reason'),
            'verified_by' => Auth::id(),
            'verified_at' => $now,
        ]);

        $studentName = $payment->studentDue?->user?->name ?? 'Mahasiswa';

        return redirect()->back()->with('success', "Pembayaran dari {$studentName} telah ditolak dengan alasan yang tersimpan.");
    }
}
