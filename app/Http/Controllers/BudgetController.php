<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month_year', Carbon::now()->format('Y-m'));

        $expenseCategories = Category::where('type', 'expense')->orderBy('name')->get();

        $budgetsData = $expenseCategories->map(function ($category) use ($selectedMonth) {
            $budget = Budget::where('category_id', $category->id)
                ->where('month_year', $selectedMonth)
                ->first();

            $spent = Transaction::where('category_id', $category->id)
                ->where('date', 'like', "{$selectedMonth}%")
                ->sum('amount');

            $amountLimit = $budget ? $budget->amount_limit : 0;
            $percentage = $amountLimit > 0 ? round(($spent / $amountLimit) * 100, 1) : 0;
            $remaining = $amountLimit > 0 ? max(0, $amountLimit - $spent) : 0;

            return [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'amount_limit' => $amountLimit,
                'spent' => $spent,
                'remaining' => $remaining,
                'percentage' => $percentage,
                'is_over' => $amountLimit > 0 && $spent > $amountLimit,
                'has_budget' => $budget !== null,
            ];
        });

        return view('budgets.index', compact('budgetsData', 'selectedMonth', 'expenseCategories'));
    }

    public function storeOrUpdate(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount_limit' => 'required|numeric|min:0',
            'month_year' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        Budget::updateOrCreate(
            [
                'category_id' => $validated['category_id'],
                'month_year' => $validated['month_year'],
            ],
            [
                'amount_limit' => $validated['amount_limit'],
            ]
        );

        return redirect()->back()->with('success', 'Batas Anggaran berhasil diperbarui!');
    }
}
