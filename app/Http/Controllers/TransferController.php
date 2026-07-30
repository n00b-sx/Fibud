<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransfer;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = AccountTransfer::with(['fromAccount', 'toAccount'])
            ->latest('date')
            ->latest('id')
            ->paginate(15);

        $accounts = Account::orderBy('name')->get();

        return view('transfers.index', compact('transfers', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id|different:from_account_id',
            'amount' => 'required|numeric|gt:0',
            'admin_fee' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ], [
            'to_account_id.different' => 'Rekening tujuan harus berbeda dari rekening asal.',
            'amount.gt' => 'Nominal transfer harus lebih besar dari 0.',
        ]);

        $fromAccount = Account::findOrFail($validated['from_account_id']);
        $toAccount = Account::findOrFail($validated['to_account_id']);

        $amount = (float) $validated['amount'];
        $adminFee = (float) ($validated['admin_fee'] ?? 0);
        $totalDeduction = $amount + $adminFee;

        // Cek kecukupan saldo rekening asal
        if ($fromAccount->balance < $totalDeduction) {
            return redirect()->back()->withErrors([
                'amount' => "Saldo {$fromAccount->name} (Rp " . number_format($fromAccount->balance, 0, ',', '.') . ") tidak mencukupi untuk transfer Rp " . number_format($amount, 0, ',', '.') . ($adminFee > 0 ? " + biaya admin Rp " . number_format($adminFee, 0, ',', '.') : "") . "."
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $fromAccount, $toAccount, $amount, $adminFee) {
            // Dapatkan / buat kategori default mutasi
            $catOut = Category::firstOrCreate(['name' => 'Transfer Antar Rekening', 'type' => 'expense']);
            $catIn = Category::firstOrCreate(['name' => 'Transfer Antar Rekening', 'type' => 'income']);
            $catFee = Category::firstOrCreate(['name' => 'Biaya Admin Bank / E-Wallet', 'type' => 'expense']);

            $notesText = !empty($validated['notes']) ? " ({$validated['notes']})" : "";

            // 1. Transaksi Pengeluaran di Rekening Asal
            $fromTx = Transaction::create([
                'account_id' => $fromAccount->id,
                'category_id' => $catOut->id,
                'amount' => $amount,
                'date' => $validated['date'],
                'description' => "Transfer ke {$toAccount->name}{$notesText}",
            ]);

            // 2. Transaksi Pemasukan di Rekening Tujuan
            $toTx = Transaction::create([
                'account_id' => $toAccount->id,
                'category_id' => $catIn->id,
                'amount' => $amount,
                'date' => $validated['date'],
                'description' => "Transfer dari {$fromAccount->name}{$notesText}",
            ]);

            // 3. Transaksi Biaya Admin (jika ada)
            $feeTx = null;
            if ($adminFee > 0) {
                $feeTx = Transaction::create([
                    'account_id' => $fromAccount->id,
                    'category_id' => $catFee->id,
                    'amount' => $adminFee,
                    'date' => $validated['date'],
                    'description' => "Biaya Admin Transfer {$fromAccount->name} ke {$toAccount->name}",
                ]);
            }

            // 4. Catatan Mutasi Utama
            AccountTransfer::create([
                'from_account_id' => $fromAccount->id,
                'to_account_id' => $toAccount->id,
                'amount' => $amount,
                'admin_fee' => $adminFee,
                'date' => $validated['date'],
                'notes' => $validated['notes'] ?? null,
                'from_transaction_id' => $fromTx->id,
                'to_transaction_id' => $toTx->id,
                'fee_transaction_id' => $feeTx?->id,
            ]);
        });

        return redirect()->back()->with('success', "Mutasi sebesar Rp " . number_format($amount, 0, ',', '.') . " dari {$fromAccount->name} ke {$toAccount->name} berhasil diproses!");
    }

    public function destroy(AccountTransfer $transfer)
    {
        DB::transaction(function () use ($transfer) {
            // Transaksi terkait akan terhapus via cascade FK, namun dipastikan di sini
            if ($transfer->fromTransaction) $transfer->fromTransaction()->delete();
            if ($transfer->toTransaction) $transfer->toTransaction()->delete();
            if ($transfer->feeTransaction) $transfer->feeTransaction()->delete();

            $transfer->delete();
        });

        return redirect()->back()->with('success', 'Data mutasi berhasil dihapus dan saldo telah dipulihkan!');
    }
}
