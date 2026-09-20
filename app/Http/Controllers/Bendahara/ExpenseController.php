<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bendahara\StoreExpenseRequest;
use App\Models\FinancialTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Store a class expense recorded by the treasurer.
     */
    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('receipts', 'public');
        }

        DB::transaction(function () use ($request, $receiptPath) {
            FinancialTransaction::create([
                'type' => 'expense',
                'amount' => $request->validated('amount'),
                'transaction_date' => $request->validated('transaction_date'),
                'category' => $request->validated('category'),
                'description' => $request->validated('description'),
                'receipt_path' => $receiptPath,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Pengeluaran kas kelas berhasil dicatat ke dalam buku kas!');
    }
}
