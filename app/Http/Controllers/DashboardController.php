<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->format('Y-m');

        // Accounts list with dynamic balances
        $accounts = Account::orderBy('name')->get();
        $totalBalance = $accounts->sum(fn ($acc) => $acc->balance);

        // All-time Metrics
        $totalIncome = Transaction::whereHas('category', fn ($q) => $q->where('type', 'income'))->sum('amount');
        $totalExpense = Transaction::whereHas('category', fn ($q) => $q->where('type', 'expense'))->sum('amount');

        // Current Month Metrics
        $monthlyIncome = Transaction::whereHas('category', fn ($q) => $q->where('type', 'income'))
            ->where('date', 'like', "{$currentMonth}%")
            ->sum('amount');

        $monthlyExpense = Transaction::whereHas('category', fn ($q) => $q->where('type', 'expense'))
            ->where('date', 'like', "{$currentMonth}%")
            ->sum('amount');

        $netFlow = $monthlyIncome - $monthlyExpense;

        // Recent 5 transactions with items
        $recentTransactions = Transaction::with(['category', 'account', 'items'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // Expense categories breakdown for current month (ApexCharts Donut)
        $expenseBreakdown = Category::where('type', 'expense')
            ->withSum(['transactions as total' => function ($q) use ($currentMonth) {
                $q->where('date', 'like', "{$currentMonth}%");
            }], 'amount')
            ->get()
            ->filter(fn ($cat) => $cat->total > 0)
            ->map(fn ($cat) => [
                'name' => $cat->name,
                'total' => (float) $cat->total,
            ])
            ->values();

        // Monthly trend for the last 6 months (ApexCharts Area/Line)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $mKey = $monthDate->format('Y-m');
            $mName = $monthDate->isoFormat('MMM Y');

            $inc = Transaction::whereHas('category', fn ($q) => $q->where('type', 'income'))
                ->where('date', 'like', "{$mKey}%")
                ->sum('amount');

            $exp = Transaction::whereHas('category', fn ($q) => $q->where('type', 'expense'))
                ->where('date', 'like', "{$mKey}%")
                ->sum('amount');

            $monthlyTrend[] = [
                'month' => $mName,
                'income' => (float) $inc,
                'expense' => (float) $exp,
            ];
        }

        // Budget summary for current month
        $budgetProgress = Budget::with('category')
            ->where('month_year', $currentMonth)
            ->get()
            ->map(function ($budget) use ($currentMonth) {
                $spent = Transaction::where('category_id', $budget->category_id)
                    ->where('date', 'like', "{$currentMonth}%")
                    ->sum('amount');

                $percentage = $budget->amount_limit > 0 ? min(100, round(($spent / $budget->amount_limit) * 100, 1)) : 0;
                $isOver = $spent > $budget->amount_limit;

                return [
                    'category_name' => $budget->category->name,
                    'limit' => $budget->amount_limit,
                    'spent' => $spent,
                    'remaining' => max(0, $budget->amount_limit - $spent),
                    'percentage' => $percentage,
                    'is_over' => $isOver,
                ];
            });

        $categories = Category::orderBy('type')->orderBy('name')->get();

        return view('dashboard', compact(
            'accounts',
            'totalBalance',
            'totalIncome',
            'totalExpense',
            'monthlyIncome',
            'monthlyExpense',
            'netFlow',
            'recentTransactions',
            'expenseBreakdown',
            'monthlyTrend',
            'budgetProgress',
            'categories',
            'currentMonth'
        ));
    }
}
