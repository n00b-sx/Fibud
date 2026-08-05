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
        $currentMonthReal = Carbon::now()->format('Y-m');
        $selectedMonth = $request->input('period', $currentMonthReal);

        // Sanitize Y-m format
        if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = $currentMonthReal;
        }

        try {
            $selectedMonthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
        } catch (\Exception $e) {
            $selectedMonth = $currentMonthReal;
            $selectedMonthDate = Carbon::now();
        }

        $selectedMonthLabel = $selectedMonthDate->isoFormat('MMMM Y');
        $prevMonthDate = $selectedMonthDate->copy()->subMonth();
        $prevMonth = $prevMonthDate->format('Y-m');
        $prevMonthLabel = $prevMonthDate->isoFormat('MMMM Y');

        // Accounts list with dynamic balances
        $accounts = Account::orderBy('name')->get();
        $totalBalance = $accounts->sum(fn ($acc) => $acc->balance);

        // All-time Metrics
        $totalIncome = Transaction::whereHas('category', fn ($q) => $q->where('type', 'income'))->sum('amount');
        $totalExpense = Transaction::whereHas('category', fn ($q) => $q->where('type', 'expense'))->sum('amount');

        // Selected Period Metrics
        $monthlyIncome = Transaction::whereHas('category', fn ($q) => $q->where('type', 'income'))
            ->where('date', 'like', "{$selectedMonth}%")
            ->sum('amount');

        $monthlyExpense = Transaction::whereHas('category', fn ($q) => $q->where('type', 'expense'))
            ->where('date', 'like', "{$selectedMonth}%")
            ->sum('amount');

        $netFlow = $monthlyIncome - $monthlyExpense;

        // Previous Period Metrics for MoM Comparison
        $prevMonthlyIncome = Transaction::whereHas('category', fn ($q) => $q->where('type', 'income'))
            ->where('date', 'like', "{$prevMonth}%")
            ->sum('amount');

        $prevMonthlyExpense = Transaction::whereHas('category', fn ($q) => $q->where('type', 'expense'))
            ->where('date', 'like', "{$prevMonth}%")
            ->sum('amount');

        $incomeChange = $prevMonthlyIncome > 0 
            ? round((($monthlyIncome - $prevMonthlyIncome) / $prevMonthlyIncome) * 100, 1) 
            : null;

        $expenseChange = $prevMonthlyExpense > 0 
            ? round((($monthlyExpense - $prevMonthlyExpense) / $prevMonthlyExpense) * 100, 1) 
            : null;

        // Transactions list for selected period (or recent overall)
        $recentTransactions = Transaction::with(['category', 'account', 'items'])
            ->where('date', 'like', "{$selectedMonth}%")
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Fallback to recent 5 if no transactions in selected historical period
        if ($recentTransactions->isEmpty()) {
            $recentTransactions = Transaction::with(['category', 'account', 'items'])
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get();
        }

        // Expense categories breakdown for selected month (ApexCharts Donut)
        $expenseBreakdown = Category::where('type', 'expense')
            ->withSum(['transactions as total' => function ($q) use ($selectedMonth) {
                $q->where('date', 'like', "{$selectedMonth}%");
            }], 'amount')
            ->get()
            ->filter(fn ($cat) => $cat->total > 0)
            ->map(fn ($cat) => [
                'name' => $cat->name,
                'total' => (float) $cat->total,
            ])
            ->values();

        // Monthly trend for the 6 months ending at selected month
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $mDate = $selectedMonthDate->copy()->subMonths($i);
            $mKey = $mDate->format('Y-m');
            $mName = $mDate->isoFormat('MMM Y');

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

        // Budget summary for selected month
        $budgetProgress = Budget::with('category')
            ->where('month_year', $selectedMonth)
            ->get()
            ->map(function ($budget) use ($selectedMonth) {
                $spent = Transaction::where('category_id', $budget->category_id)
                    ->where('date', 'like', "{$selectedMonth}%")
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

        // Available historical months list with recorded transactions for quick selector dropdown
        $availableMonths = Transaction::selectRaw("DISTINCT strftime('%Y-%m', date) as month_year")
            ->orderBy('month_year', 'desc')
            ->pluck('month_year')
            ->filter()
            ->toArray();

        if (!in_array($currentMonthReal, $availableMonths)) {
            array_unshift($availableMonths, $currentMonthReal);
        }

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
            'currentMonthReal',
            'selectedMonth',
            'selectedMonthLabel',
            'prevMonthLabel',
            'incomeChange',
            'expenseChange',
            'availableMonths'
        ));
    }
}
