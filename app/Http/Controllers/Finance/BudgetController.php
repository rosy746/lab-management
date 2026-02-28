<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Budget;
use App\Models\Finance\BudgetPeriod;
use App\Models\Finance\Category;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $periods = BudgetPeriod::withCount('budgets')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(12);

        return view('finance.budgets.index', compact('periods'));
    }

    public function create()
    {
        $categories = Category::where('type', 'expense')->where('is_active', true)->get();
        return view('finance.budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month'        => 'required|integer|min:1|max:12',
            'year'         => 'required|integer|min:2024',
            'total_budget' => 'required|numeric|min:0',
            'notes'        => 'nullable|string',
            'budgets'      => 'nullable|array',
            'budgets.*.category_id' => 'required|exists:finance.categories,id',
            'budgets.*.amount'      => 'required|numeric|min:0',
        ]);

        // Buat atau update periode
        $period = BudgetPeriod::updateOrCreate(
            ['month' => $validated['month'], 'year' => $validated['year']],
            [
                'name'         => BudgetPeriod::generateName($validated['month'], $validated['year']),
                'total_budget' => $validated['total_budget'],
                'notes'        => $validated['notes'] ?? null,
                'created_by'   => auth('finance')->id(),
            ]
        );

        // Simpan anggaran per kategori
        if (!empty($validated['budgets'])) {
            foreach ($validated['budgets'] as $item) {
                Budget::updateOrCreate(
                    ['budget_period_id' => $period->id, 'category_id' => $item['category_id']],
                    ['amount' => $item['amount']]
                );
            }
        }

        return redirect()
            ->route('finance.budgets.index')
            ->with('success', "Anggaran {$period->name} berhasil disimpan.");
    }

    public function edit(BudgetPeriod $budget)
    {
        $period     = $budget;
        $categories = Category::where('type', 'expense')->where('is_active', true)->get();
        $budgets    = Budget::where('budget_period_id', $period->id)
                            ->pluck('amount', 'category_id');

        return view('finance.budgets.edit', compact('period', 'categories', 'budgets'));
    }

    public function update(Request $request, BudgetPeriod $budget)
    {
        $period    = $budget;
        $validated = $request->validate([
            'total_budget' => 'required|numeric|min:0',
            'notes'        => 'nullable|string',
            'status'       => 'required|in:active,closed',
            'budgets'      => 'nullable|array',
            'budgets.*.category_id' => 'required|exists:finance.categories,id',
            'budgets.*.amount'      => 'required|numeric|min:0',
        ]);

        $period->update([
            'total_budget' => $validated['total_budget'],
            'notes'        => $validated['notes'] ?? null,
            'status'       => $validated['status'],
        ]);

        if (!empty($validated['budgets'])) {
            foreach ($validated['budgets'] as $item) {
                Budget::updateOrCreate(
                    ['budget_period_id' => $period->id, 'category_id' => $item['category_id']],
                    ['amount' => $item['amount']]
                );
            }
        }

        return redirect()
            ->route('finance.budgets.index')
            ->with('success', "Anggaran {$period->name} berhasil diperbarui.");
    }

    public function destroy(BudgetPeriod $budget)
    {
        $name = $budget->name;
        $budget->delete();

        return back()->with('success', "Anggaran {$name} berhasil dihapus.");
    }
}
