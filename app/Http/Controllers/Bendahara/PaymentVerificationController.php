<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\RejectPaymentRequest;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentVerificationController extends Controller
{
    /**
     * Approve a pending student payment.
     */
    public function approve(Payment $payment): RedirectResponse
    {
        if (! $payment->isPending()) {
            return redirect()->back()->with('error', 'Pembayaran ini sudah tidak dalam status menunggu verifikasi.');
        }

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
            'rejection_reason' => $request->input('rejection_reason', 'Bukti tidak valid atau nominal tidak sesuai.'),
            'verified_by' => Auth::id(),
            'verified_at' => $now,
        ]);

        $studentName = $payment->studentDue?->user?->name ?? 'Mahasiswa';

        return redirect()->back()->with('success', "Pembayaran dari {$studentName} telah ditolak.");
    }
}
