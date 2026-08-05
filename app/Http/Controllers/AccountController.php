<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::withCount('transactions')->get();

        return view('accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'nullable|string|in:bank,ewallet,cash',
            'account_number' => 'nullable|string|max:50',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        if (empty($validated['type'])) {
            $validated['type'] = 'bank';
        }

        Account::create($validated);

        return redirect()->back()->with('success', 'Rekening baru berhasil ditambahkan!');
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'nullable|string|in:bank,ewallet,cash',
            'account_number' => 'nullable|string|max:50',
            'initial_balance' => 'required|numeric|min:0',
        ]);

        if (empty($validated['type'])) {
            $validated['type'] = 'bank';
        }

        $account->update($validated);

        return redirect()->back()->with('success', 'Data Rekening berhasil diperbarui!');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->back()->with('success', 'Rekening berhasil dihapus!');
    }
}
