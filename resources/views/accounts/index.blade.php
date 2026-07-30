@extends('layouts.app')

@section('title', 'Kelola Rekening & Dompet - Fibud')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Rekening & Dompet</h1>
            <p class="text-xs sm:text-sm text-gray-500">Atur rekening bank, e-wallet, dan sumber dana tunai Anda</p>
        </div>
        <div>
            <button type="button" class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-orange-500 text-white hover:bg-orange-600 shadow-sm transition" data-hs-overlay="#hs-add-account-modal">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                + Tambah Rekening
            </button>
        </div>
    </div>

    <!-- ACCOUNTS GRID (PURE WHITE CARDS + SHADOW-SM ON CREME BG) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($accounts as $acc)
        <div class="flex flex-col bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-x-3">
                    <span class="inline-flex items-center justify-center size-10 rounded-xl bg-orange-50 text-orange-600">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                    </span>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $acc->name }}</h3>
                        <p class="text-xs text-gray-500">No. Rek / Akun: {{ $acc->account_number ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-x-1">
                    <button type="button" class="p-1.5 text-orange-600 hover:text-orange-800 rounded-lg hover:bg-orange-50 transition" data-hs-overlay="#hs-edit-account-modal-{{ $acc->id }}" title="Edit Rekening">
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"/></svg>
                    </button>

                    <form action="{{ route('accounts.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-rose-600 hover:text-rose-800 rounded-lg hover:bg-rose-50 transition" title="Hapus Rekening">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-baseline">
                <span class="text-xs text-gray-500 font-medium">Saldo Rekening:</span>
                <span class="text-lg font-extrabold text-gray-900">Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
            </div>
            <p class="text-[11px] text-gray-400 text-end mt-0.5">Saldo awal: Rp {{ number_format($acc->initial_balance, 0, ',', '.') }}</p>
        </div>

        <!-- MODAL EDIT REKENING -->
        <div id="hs-edit-account-modal-{{ $acc->id }}" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
            <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
                <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
                    <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">Edit Rekening: {{ $acc->name }}</h3>
                        <button type="button" class="size-8 inline-flex justify-center items-center rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" data-hs-overlay="#hs-edit-account-modal-{{ $acc->id }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('accounts.update', $acc->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="p-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Nama Rekening / Dompet</label>
                                <input type="text" name="name" value="{{ $acc->name }}" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Nomor Rekening / Akun (Opsional)</label>
                                <input type="text" name="account_number" value="{{ $acc->account_number }}" placeholder="Misal: 1234567890" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Saldo Awal (Rp)</label>
                                <input type="text" name="initial_balance" value="{{ number_format($acc->initial_balance, 0, '', '') }}" data-currency-input required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm font-bold focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>

                        <div class="flex justify-end gap-x-2 py-3 px-4 border-t border-gray-200">
                            <button type="button" class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-edit-account-modal-{{ $acc->id }}">Batal</button>
                            <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- MODAL TAMBAH REKENING BARU -->
    <div id="hs-add-account-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
            <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
                <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                    <h3 class="font-bold text-gray-900">Tambah Rekening Baru</h3>
                    <button type="button" class="size-8 inline-flex justify-center items-center rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" data-hs-overlay="#hs-add-account-modal">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('accounts.store') }}" method="POST">
                    @csrf
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Nama Rekening / Dompet</label>
                            <input type="text" name="name" required placeholder="Misal: Bank Mandiri / OVO" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Nomor Rekening / Akun (Opsional)</label>
                            <input type="text" name="account_number" placeholder="Misal: 9876543210" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Saldo Awal (Rp)</label>
                            <input type="text" name="initial_balance" value="0" data-currency-input required placeholder="0" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm font-bold focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-x-2 py-3 px-4 border-t border-gray-200">
                        <button type="button" class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-add-account-modal">Batal</button>
                        <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm">Simpan Rekening</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
