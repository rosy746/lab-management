<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Transaction;
use App\Models\Finance\Account;
use App\Models\Finance\BudgetPeriod;
use App\Models\Finance\Budget;

class DashboardController extends Controller
{
    public function index()
    {
        $month = now()->month;
        $year  = now()->year;

        $totalIncome  = Transaction::income()->byPeriod($month, $year)->whereNull('deleted_at')->sum('amount');
        $totalExpense = Transaction::expense()->byPeriod($month, $year)->whereNull('deleted_at')->sum('amount');
        $kasBalance   = Account::where('type', 'cash')->where('is_active', true)->sum('balance');
        $period       = BudgetPeriod::where('month', $month)->where('year', $year)->first();

        $budgets = $period
            ? Budget::with('category')->where('budget_period_id', $period->id)->get()
            : collect();

        $recentTransactions = Transaction::with(['category', 'account'])
            ->whereNull('deleted_at')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // ── Bar Chart: 6 bulan terakhir ───────────────────
        $chartMonths  = [];
        $chartIncome  = [];
        $chartExpense = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $m    = $date->month;
            $y    = $date->year;

            $chartMonths[]  = $date->isoFormat('MMM YY');
            $chartIncome[]  = (float) Transaction::income()->byPeriod($m, $y)->whereNull('deleted_at')->sum('amount');
            $chartExpense[] = (float) Transaction::expense()->byPeriod($m, $y)->whereNull('deleted_at')->sum('amount');
        }

        // ── Pie Chart: pengeluaran per kategori bulan ini ─
        $expenseByCategory = Transaction::with('category')
            ->expense()
            ->byPeriod($month, $year)
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('category_id')
            ->map(fn($trx) => [
                'name'  => $trx->first()->category->name,
                'total' => (float) $trx->sum('amount'),
                'color' => $trx->first()->category->color,
            ])
            ->values();

        return view('finance.dashboard.index', compact(
            'totalIncome', 'totalExpense', 'kasBalance',
            'period', 'budgets', 'recentTransactions',
            'chartMonths', 'chartIncome', 'chartExpense',
            'expenseByCategory'
        ));
    }
}