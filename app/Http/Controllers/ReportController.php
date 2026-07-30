<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function getReportData(Request $request)
    {
        $query = Transaction::with(['category', 'account', 'items']);

        $period = $request->get('period', 'this_month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $monthYear = $request->get('month_year'); // e.g. "2026-07"
        $accountId = $request->get('account_id');
        $categoryId = $request->get('category_id');

        $periodLabel = 'Bulan Ini (' . date('F Y') . ')';

        // Filter Rekening & Kategori
        if ($accountId) {
            $query->where('account_id', $accountId);
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filter Waktu Fleksibel
        if ($monthYear) {
            $dateObj = Carbon::createFromFormat('Y-m', $monthYear);
            $query->whereYear('date', $dateObj->year)->whereMonth('date', $dateObj->month);
            $periodLabel = 'Bulan ' . $dateObj->isoFormat('MMMM Y');
        } elseif ($startDate || $endDate) {
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
                $periodLabel = Carbon::parse($startDate)->isoFormat('D MMMM Y') . ' s/d ' . Carbon::parse($endDate)->isoFormat('D MMMM Y');
            } elseif ($startDate) {
                $query->whereDate('date', '>=', $startDate);
                $periodLabel = 'Mulai ' . Carbon::parse($startDate)->isoFormat('D MMMM Y');
            } else {
                $query->whereDate('date', '<=', $endDate);
                $periodLabel = 'Sampai ' . Carbon::parse($endDate)->isoFormat('D MMMM Y');
            }
        } elseif ($period) {
            switch ($period) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    $periodLabel = 'Hari Ini (' . Carbon::today()->isoFormat('D MMMM Y') . ')';
                    break;
                case 'this_week':
                    $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    $periodLabel = 'Minggu Ini (' . Carbon::now()->startOfWeek()->isoFormat('D MMMM') . ' - ' . Carbon::now()->endOfWeek()->isoFormat('D MMMM Y') . ')';
                    break;
                case 'this_month':
                    $query->whereYear('date', Carbon::now()->year)->whereMonth('date', Carbon::now()->month);
                    $periodLabel = 'Bulan Ini (' . Carbon::now()->isoFormat('MMMM Y') . ')';
                    break;
                case 'this_year':
                    $query->whereYear('date', Carbon::now()->year);
                    $periodLabel = 'Tahun ' . Carbon::now()->year;
                    break;
                case 'all':
                    $periodLabel = 'Semua Waktu';
                    break;
            }
        }

        $transactions = $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

        $totalIncome = $transactions->filter(fn ($t) => $t->category?->type === 'income')->sum('amount');
        $totalExpense = $transactions->filter(fn ($t) => $t->category?->type === 'expense')->sum('amount');
        $netFlow = $totalIncome - $totalExpense;

        // Rekapitulasi per Kategori Pengeluaran
        $expenseCategoryBreakdown = $transactions->filter(fn ($t) => $t->category?->type === 'expense')
            ->groupBy('category.name')
            ->map(function ($items, $catName) use ($totalExpense) {
                $sum = $items->sum('amount');
                return [
                    'name' => $catName,
                    'total' => $sum,
                    'percentage' => $totalExpense > 0 ? round(($sum / $totalExpense) * 100, 1) : 0,
                    'count' => $items->count(),
                ];
            })->sortByDesc('total');

        // Rekapitulasi per Kategori Pemasukan
        $incomeCategoryBreakdown = $transactions->filter(fn ($t) => $t->category?->type === 'income')
            ->groupBy('category.name')
            ->map(function ($items, $catName) use ($totalIncome) {
                $sum = $items->sum('amount');
                return [
                    'name' => $catName,
                    'total' => $sum,
                    'percentage' => $totalIncome > 0 ? round(($sum / $totalIncome) * 100, 1) : 0,
                    'count' => $items->count(),
                ];
            })->sortByDesc('total');

        $categories = Category::orderBy('type')->orderBy('name')->get();
        $accounts = Account::orderBy('name')->get();

        return [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netFlow' => $netFlow,
            'expenseCategoryBreakdown' => $expenseCategoryBreakdown,
            'incomeCategoryBreakdown' => $incomeCategoryBreakdown,
            'periodLabel' => $periodLabel,
            'categories' => $categories,
            'accounts' => $accounts,
            'filters' => [
                'period' => $period,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'month_year' => $monthYear,
                'account_id' => $accountId,
                'category_id' => $categoryId,
            ]
        ];
    }

    public function index(Request $request)
    {
        $data = $this->getReportData($request);

        return view('reports.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = Pdf::loadView('reports.pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Laporan_Keuangan_Fibud_' . str_replace([' ', '/', '\\'], '_', $data['periodLabel']) . '.pdf';

        return $pdf->download($filename);
    }
}
