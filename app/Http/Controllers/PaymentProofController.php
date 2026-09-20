<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentProofController extends Controller
{
    /**
     * Securely serve payment proof files with authorization.
     */
    public function show(Payment $payment): StreamedResponse
    {
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Silakan masuk untuk mengakses berkas ini.');
        }

        // Authorization check:
        // Bendahara can view any payment proof.
        // Mahasiswa can ONLY view their own payment proof.
        if ($user->isMahasiswa()) {
            $studentId = $payment->studentDue?->user_id;
            if (! $studentId || $studentId !== $user->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat bukti pembayaran ini.');
            }
        }

        if (! $payment->proof_file_path) {
            abort(404, 'Bukti pembayaran tidak tersedia.');
        }

        // Support local private disk first, fallback to public disk for legacy uploads
        $disk = null;
        if (Storage::disk('local')->exists($payment->proof_file_path)) {
            $disk = 'local';
        } elseif (Storage::disk('public')->exists($payment->proof_file_path)) {
            $disk = 'public';
        }

        if (! $disk) {
            abort(404, 'Berkas bukti pembayaran tidak ditemukan di server.');
        }

        return Storage::disk($disk)->response($payment->proof_file_path);
    }
}
