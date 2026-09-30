<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionReceiptController extends Controller
{
    /**
     * Securely serve financial transaction receipts or proofs.
     */
    public function show(FinancialTransaction $transaction): StreamedResponse
    {
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Silakan masuk untuk mengakses berkas ini.');
        }

        // Authorization check for payment-based transactions:
        // Mahasiswa may only view their own payment proof; Bendahara can view any.
        if ($user->isMahasiswa() && ($transaction->isPaymentBased() || $transaction->payment_id !== null)) {
            $studentId = $transaction->payment?->studentDue?->user_id;
            if (! $studentId || $studentId !== $user->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti transfer mahasiswa lain.');
            }
        }

        $path = $transaction->receipt_path;

        // If no explicit receipt_path, check if transaction has payment proof
        if (! $path && $transaction->payment?->proof_file_path) {
            $path = $transaction->payment->proof_file_path;
        }

        if (! $path) {
            abort(404, 'Bukti nota/kuitansi transaksi tidak tersedia.');
        }

        $disk = null;
        if (Storage::disk('local')->exists($path)) {
            $disk = 'local';
        } elseif (Storage::disk('public')->exists($path)) {
            $disk = 'public';
        }

        if (! $disk) {
            abort(404, 'Berkas bukti transaksi tidak ditemukan di server.');
        }

        return Storage::disk($disk)->response($path);
    }
}
