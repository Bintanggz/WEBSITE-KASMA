<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\StoreCashPaymentRequest;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashPaymentController extends Controller
{
    /**
     * Store a direct cash payment recorded by the treasurer.
     */
    public function store(StoreCashPaymentRequest $request): RedirectResponse
    {
        $dueId = $request->input('student_due_id');
        $studentName = '';

        try {
            DB::transaction(function () use ($dueId, &$studentName) {
                $due = StudentDue::lockForUpdate()->findOrFail($dueId);

                if ($due->isPaid()) {
                    throw new \Exception('Kewajiban kas ini sudah lunas sebelumnya.');
                }

                $now = Carbon::now();
                $studentName = $due->user->name ?? 'Mahasiswa';

                // If student had a pending payment submission for this due, resolve it
                Payment::where('student_due_id', $due->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'rejection_reason' => 'Digantikan oleh setoran tunai langsung ke bendahara kelas.',
                        'verified_by' => Auth::id(),
                        'verified_at' => $now,
                    ]);

                $payment = Payment::create([
                    'student_due_id' => $due->id,
                    'amount' => $due->amount,
                    'payment_method' => 'cash',
                    'proof_file_path' => null,
                    'payment_date' => $now,
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
                    'amount' => $due->amount,
                    'transaction_date' => $now->toDateString(),
                    'category' => 'Iuran Kas Tunai',
                    'description' => 'Iuran ' . ($due->cashPeriod->name ?? 'Kas') . ' (Tunai) - ' . $due->user->name . ' (' . $due->user->nim . ')',
                    'payment_id' => $payment->id,
                    'receipt_path' => null,
                    'created_by' => Auth::id(),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mencatat pembayaran tunai: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "Setoran tunai untuk {$studentName} berhasil dicatat dan masuk ke kas kelas!");
    }
}
