<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['category', 'account', 'items']);

        // Filter Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter Rekening
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // Filter Tipe (Pemasukan / Pengeluaran)
        if ($request->filled('type')) {
            $query->whereHas('category', fn ($q) => $q->where('type', $request->type));
        }

        // Filter Pencarian Teks
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('source_destination', 'like', '%' . $request->search . '%')
                  ->orWhereHas('items', fn ($iq) => $iq->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        // Filter Rentang Waktu / Periode
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('date', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereYear('date', Carbon::now()->year)
                          ->whereMonth('date', Carbon::now()->month);
                    break;
                case 'this_year':
                    $query->whereYear('date', Carbon::now()->year);
                    break;
            }
        }

        // Filter Rentang Tanggal Manual (Custom Range)
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Hitung Akumulasi Total Hasil Filter SEBELUM Paginasi
        $filteredIncome = (clone $query)
            ->whereHas('category', fn ($q) => $q->where('type', 'income'))
            ->sum('amount');

        $filteredExpense = (clone $query)
            ->whereHas('category', fn ($q) => $q->where('type', 'expense'))
            ->sum('amount');

        $filteredNet = $filteredIncome - $filteredExpense;

        // Execute Paginasi & Relasi
        $transactions = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10);
        $categories = Category::orderBy('type')->orderBy('name')->get();
        $accounts = Account::orderBy('name')->get();

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'accounts',
            'filteredIncome',
            'filteredExpense',
            'filteredNet'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'source_destination' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $itemsData = [];
            $computedTotal = 0;

            if ($request->has('items') && is_array($request->items) && count($request->items) > 0) {
                foreach ($request->items as $item) {
                    if (empty($item['name'])) continue;
                    
                    $qty = (int) ($item['quantity'] ?? 1);
                    $price = (float) ($item['unit_price'] ?? 0);
                    $discount = (float) ($item['discount'] ?? 0);
                    $subtotal = max(0, ($qty * $price) - $discount);

                    $computedTotal += $subtotal;
                    $itemsData[] = [
                        'name' => $item['name'],
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount' => $discount,
                        'subtotal' => $subtotal,
                    ];
                }
            }

            $finalAmount = count($itemsData) > 0 ? $computedTotal : (float) ($validated['amount'] ?? 0);

            if ($finalAmount <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Nominal transaksi harus lebih besar dari 0.',
                ]);
            }

            $transaction = Transaction::create([
                'category_id' => $validated['category_id'],
                'account_id' => $validated['account_id'],
                'amount' => $finalAmount,
                'date' => $validated['date'],
                'description' => $validated['description'],
                'source_destination' => $validated['source_destination'] ?? null,
            ]);

            foreach ($itemsData as $item) {
                $transaction->items()->create($item);
            }
        });

        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'source_destination' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.name' => 'required_with:items|string|max:255',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request, $transaction) {
            $itemsData = [];
            $computedTotal = 0;

            if ($request->has('items') && is_array($request->items) && count($request->items) > 0) {
                foreach ($request->items as $item) {
                    if (empty($item['name'])) continue;
                    
                    $qty = (int) ($item['quantity'] ?? 1);
                    $price = (float) ($item['unit_price'] ?? 0);
                    $discount = (float) ($item['discount'] ?? 0);
                    $subtotal = max(0, ($qty * $price) - $discount);

                    $computedTotal += $subtotal;
                    $itemsData[] = [
                        'name' => $item['name'],
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount' => $discount,
                        'subtotal' => $subtotal,
                    ];
                }
            }

            $finalAmount = count($itemsData) > 0 ? $computedTotal : (float) ($validated['amount'] ?? 0);

            if ($finalAmount <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Nominal transaksi harus lebih besar dari 0.',
                ]);
            }

            $transaction->update([
                'category_id' => $validated['category_id'],
                'account_id' => $validated['account_id'],
                'amount' => $finalAmount,
                'date' => $validated['date'],
                'description' => $validated['description'],
                'source_destination' => $validated['source_destination'] ?? null,
            ]);

            // Re-sync transaction items
            $transaction->items()->delete();
            foreach ($itemsData as $item) {
                $transaction->items()->create($item);
            }
        });

        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->back()->with('success', 'Transaksi berhasil dihapus!');
    }
}
