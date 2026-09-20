<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\StoreTransactionRequest;
use App\Http\Requests\Bendahara\UpdateTransactionRequest;
use App\Models\FinancialTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FinancialTransactionController extends Controller
{
    /**
     * Display the transactions page with financial summary and filters.
     */
    public function index(Request $request): View
    {
        // Balance strictly computed from the ledger
        $totalIncome = (float) FinancialTransaction::where('type', 'income')->sum('amount');
        $totalExpense = (float) FinancialTransaction::where('type', 'expense')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        $incomeCount = FinancialTransaction::where('type', 'income')->count();
        $expenseCount = FinancialTransaction::where('type', 'expense')->count();
        $totalCount = FinancialTransaction::count();

        $type = $request->query('type', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');

        $query = FinancialTransaction::with(['creator', 'payment.studentDue.user', 'payment.studentDue.cashPeriod']);

        if (in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        if ($startDate) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        if ($search) {
            $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $operator) {
                $q->where('description', $operator, "%{$search}%")
                  ->orWhere('category', $operator, "%{$search}%");
            });
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(15)->withQueryString();

        return view('bendahara.transaksi.index', compact(
            'transactions',
            'currentBalance',
            'totalIncome',
            'totalExpense',
            'incomeCount',
            'expenseCount',
            'totalCount',
            'type',
            'startDate',
            'endDate',
            'search'
        ));
    }

    /**
     * Store a manual financial transaction (income or expense).
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('receipts', 'local');
        }

        DB::transaction(function () use ($request, $receiptPath) {
            FinancialTransaction::create([
                'type' => $request->validated('type'),
                'amount' => $request->validated('amount'),
                'transaction_date' => $request->validated('transaction_date'),
                'category' => $request->validated('category'),
                'description' => $request->validated('description'),
                'payment_id' => null, // Explicitly manual
                'receipt_path' => $receiptPath,
                'created_by' => Auth::id(),
            ]);
        });

        $typeLabel = $request->validated('type') === 'income' ? 'Pemasukan' : 'Pengeluaran';

        return redirect()->route('bendahara.transaksi.index')
            ->with('success', "Transaksi {$typeLabel} manual berhasil ditambahkan ke buku kas!");
    }

    /**
     * Update a manual financial transaction.
     */
    public function update(UpdateTransactionRequest $request, FinancialTransaction $transaction): RedirectResponse
    {
        // Enforce protection for payment-based transactions
        if ($transaction->isPaymentBased() || $transaction->payment_id !== null) {
            return redirect()->route('bendahara.transaksi.index')
                ->with('error', 'Transaksi yang bersumber dari pembayaran iuran mahasiswa tidak dapat diubah secara manual.');
        }

        DB::transaction(function () use ($request, $transaction) {
            $data = [
                'type' => $request->validated('type'),
                'amount' => $request->validated('amount'),
                'transaction_date' => $request->validated('transaction_date'),
                'category' => $request->validated('category'),
                'description' => $request->validated('description'),
            ];

            if ($request->hasFile('receipt_file')) {
                // Delete old receipt file if present
                if ($transaction->receipt_path && Storage::disk('local')->exists($transaction->receipt_path)) {
                    Storage::disk('local')->delete($transaction->receipt_path);
                }
                $data['receipt_path'] = $request->file('receipt_file')->store('receipts', 'local');
            }

            $transaction->update($data);
        });

        return redirect()->route('bendahara.transaksi.index')
            ->with('success', 'Transaksi manual berhasil diperbarui!');
    }

    /**
     * Delete a manual financial transaction.
     */
    public function destroy(FinancialTransaction $transaction): RedirectResponse
    {
        // Enforce protection for payment-based transactions
        if ($transaction->isPaymentBased() || $transaction->payment_id !== null) {
            return redirect()->route('bendahara.transaksi.index')
                ->with('error', 'Transaksi yang bersumber dari pembayaran iuran mahasiswa tidak dapat dihapus secara manual.');
        }

        DB::transaction(function () use ($transaction) {
            if ($transaction->receipt_path && Storage::disk('local')->exists($transaction->receipt_path)) {
                Storage::disk('local')->delete($transaction->receipt_path);
            }
            $transaction->delete();
        });

        return redirect()->route('bendahara.transaksi.index')
            ->with('success', 'Transaksi manual berhasil dihapus dari buku kas.');
    }
}
