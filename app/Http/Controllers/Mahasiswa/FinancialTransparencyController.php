<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialTransparencyController extends Controller
{
    /**
     * Display the transparent, read-only class cash ledger for students.
     */
    public function index(Request $request): View
    {
        $totalIncome = (float) FinancialTransaction::where('type', 'income')->sum('amount');
        $totalExpense = (float) FinancialTransaction::where('type', 'expense')->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        $type = $request->query('type', 'all');

        $query = FinancialTransaction::with(['creator', 'payment.studentDue.cashPeriod']);

        if (in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(15)->withQueryString();

        return view('mahasiswa.keuangan.index', compact(
            'transactions',
            'currentBalance',
            'totalIncome',
            'totalExpense',
            'type'
        ));
    }
}
