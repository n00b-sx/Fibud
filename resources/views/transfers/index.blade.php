@extends('layouts.app')

@section('title', 'Mutasi & Top Up E-Wallet - Fibud')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Mutasi Antar Rekening & Top Up E-Wallet</h1>
            <p class="text-xs sm:text-sm text-gray-500">Pindahkan dana antar rekening bank atau top up dompet digital dengan pencatatan otomatis</p>
        </div>
        <div>
            <button type="button" class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-orange-500 text-white hover:bg-orange-600 shadow-sm transition" data-hs-overlay="#hs-add-transfer-modal">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 3 4 4-4 4"/><path d="M20 7H4"/><path d="m8 21-4-4 4-4"/><path d="M4 17h16"/></svg>
                + Mutasi / Top Up Baru
            </button>
        </div>
    </div>

    <!-- QUICK STATS CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Mutasi Recorded</span>
            <div class="mt-2 text-2xl font-extrabold text-gray-900">{{ $transfers->total() }} Kali</div>
            <p class="text-xs text-gray-500 mt-1">Riwayat pemindahan dana</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jumlah Rekening Aktif</span>
            <div class="mt-2 text-2xl font-extrabold text-orange-600">{{ $accounts->count() }} Sumber Dana</div>
            <p class="text-xs text-gray-500 mt-1">Bank, E-Wallet & Tunai</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Butuh Rekening Baru?</span>
                <p class="text-xs text-gray-500 mt-1">Tambah dompet atau bank baru</p>
            </div>
            <a href="{{ route('accounts.index') }}" class="py-1.5 px-3 text-xs font-semibold rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 transition">
                Kelola Rekening →
            </a>
        </div>
    </div>

    <!-- TRANSFERS TABLE CARD (PURE WHITE CARDS + SHADOW-SM ON CREME BG) -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-semibold text-gray-500 uppercase">
                        <th class="py-3 px-4 text-start">Tanggal</th>
                        <th class="py-3 px-4 text-start">Dari Rekening</th>
                        <th class="py-3 px-4 text-center">Arah Mutasi</th>
                        <th class="py-3 px-4 text-start">Ke Rekening / E-Wallet</th>
                        <th class="py-3 px-4 text-end">Nominal Transfer</th>
                        <th class="py-3 px-4 text-end">Biaya Admin</th>
                        <th class="py-3 px-4 text-start">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($transfers as $tf)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="py-3 px-4 whitespace-nowrap text-gray-600 text-xs sm:text-sm font-medium">
                            {{ $tf->date->format('d M Y') }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-bold text-gray-900">{{ $tf->fromAccount->name ?? 'Dihapus' }}</span>
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center size-7 rounded-full bg-orange-50 text-orange-600">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-bold text-gray-900">{{ $tf->toAccount->name ?? 'Dihapus' }}</span>
                        </td>
                        <td class="py-3 px-4 text-end font-extrabold text-gray-900 whitespace-nowrap">
                            Rp {{ number_format($tf->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-end text-xs font-semibold whitespace-nowrap {{ $tf->admin_fee > 0 ? 'text-rose-600' : 'text-gray-400' }}">
                            {{ $tf->admin_fee > 0 ? 'Rp ' . number_format($tf->admin_fee, 0, ',', '.') : 'Gratis' }}
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-600">
                            {{ $tf->notes ?? '-' }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <form action="{{ route('transfers.destroy', $tf->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat mutasi ini? Saldo kedua rekening akan dikembalikan ke posisi semula.');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 py-1 px-2 rounded-lg hover:bg-rose-50 transition">
                                    Hapus Mutasi
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500 text-sm">
                            Belum ada riwayat mutasi antar rekening atau top up E-Wallet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>

</div>

<!-- MODAL TAMBAH MUTASI / TOP UP BARU -->
<div id="hs-add-transfer-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-900">
                    Mutasi Antar Rekening / Top Up E-Wallet
                </h3>
                <button type="button" class="size-8 inline-flex justify-center items-center rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" data-hs-overlay="#hs-add-transfer-modal">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('transfers.store') }}" method="POST">
                @csrf
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="from_account_id" class="block text-sm font-medium text-gray-900 mb-1">Dari Rekening (Sumber)</label>
                            <select id="from_account_id" name="from_account_id" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="" disabled selected>-- Pilih Sumber --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="to_account_id" class="block text-sm font-medium text-gray-900 mb-1">Ke Rekening / E-Wallet (Tujuan)</label>
                            <select id="to_account_id" name="to_account_id" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                                <option value="" disabled selected>-- Pilih Tujuan --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="transfer_amount" class="block text-sm font-medium text-gray-900 mb-1">Nominal Transfer (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-gray-500 text-sm font-semibold">Rp</span>
                                <input type="text" id="transfer_amount" name="amount" data-currency-input required placeholder="0" class="py-2 ps-9 pe-3 block w-full border-gray-200 rounded-lg text-sm font-bold focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>

                        <div>
                            <label for="admin_fee" class="block text-sm font-medium text-gray-900 mb-1">Biaya Admin (Rp, Jika Ada)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-gray-500 text-sm font-semibold">Rp</span>
                                <input type="text" id="admin_fee" name="admin_fee" data-currency-input placeholder="0" class="py-2 ps-9 pe-3 block w-full border-gray-200 rounded-lg text-sm font-bold focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="transfer_date" class="block text-sm font-medium text-gray-900 mb-1">Tanggal Mutasi</label>
                            <input type="date" id="transfer_date" name="date" value="{{ date('Y-m-d') }}" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <div>
                            <label for="transfer_notes" class="block text-sm font-medium text-gray-900 mb-1">Catatan / Keterangan (Opsional)</label>
                            <input type="text" id="transfer_notes" name="notes" placeholder="Contoh: Top Up Gopay via BCA" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-x-2 py-3 px-4 border-t border-gray-200">
                    <button type="button" class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-add-transfer-modal">Batal</button>
                    <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm">Proses Mutasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
