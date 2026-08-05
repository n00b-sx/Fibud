@extends('layouts.app')

@section('title', 'Kelola Rekening & Dompet - Fibud')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Rekening & Dompet</h1>
            <p class="text-xs sm:text-sm text-gray-600">Atur rekening bank, e-wallet, dan sumber dana tunai Anda</p>
        </div>
        <div class="flex items-center gap-x-2">
            <a href="{{ route('transfers.index') }}" class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 shadow-sm transition">
                <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F504.svg" alt="Transfer" class="size-4 shrink-0" />
                Transfer / Top Up
            </a>
            <button type="button" class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#66BB6A] text-white hover:bg-[#52A456] shadow-sm transition" data-hs-overlay="#hs-add-account-modal">
                <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/2795.svg" alt="Tambah" class="size-4 shrink-0" />
                Tambah Rekening
            </button>
        </div>
    </div>

    <!-- ACCOUNTS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($accounts as $acc)
        <div class="flex flex-col bg-white rounded-xl p-5 shadow-md border-none">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-x-3">
                    <span class="inline-flex items-center justify-center size-10 rounded-xl bg-emerald-50 border border-emerald-100 shrink-0">
                        <img src="{{ $acc->openmoji_icon_url }}" alt="{{ $acc->type_label }}" class="size-6 shrink-0" />
                    </span>
                    <div>
                        <div class="flex items-center gap-x-2">
                            <h3 class="font-bold text-gray-900">{{ $acc->name }}</h3>
                            <span class="inline-flex items-center py-0.5 px-2 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $acc->type === 'ewallet' ? 'bg-purple-50 text-purple-700 border border-purple-200' : ($acc->type === 'cash' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                {{ $acc->type_label }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">No. Rek / Akun: {{ $acc->account_number ?? '-' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-x-1">
                    <button type="button" class="p-1.5 text-emerald-700 hover:text-emerald-900 rounded-lg hover:bg-emerald-50 transition" data-hs-overlay="#hs-edit-account-modal-{{ $acc->id }}" title="Edit Rekening">
                        <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/270F.svg" alt="Edit" class="size-4 shrink-0" />
                    </button>

                    <form action="{{ route('accounts.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekening ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-rose-600 hover:text-rose-800 rounded-lg hover:bg-rose-50 transition" title="Hapus Rekening">
                            <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F5D1.svg" alt="Hapus" class="size-4 shrink-0" />
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between items-baseline">
                <span class="text-xs text-gray-500 font-medium">Saldo Rekening:</span>
                <span class="text-lg font-extrabold text-gray-900">Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
            </div>
            <p class="text-[11px] text-gray-400 text-end mt-0.5">Saldo awal: Rp {{ number_format($acc->initial_balance, 0, ',', '.') }}</p>
        </div>

        <!-- MODAL EDIT REKENING -->
        <div id="hs-edit-account-modal-{{ $acc->id }}" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
            <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
                <div class="w-full flex flex-col modal-glass rounded-2xl pointer-events-auto overflow-hidden">
                    <div class="flex justify-between items-center py-3.5 px-4 modal-glass-header">
                        <h3 class="font-bold text-gray-900">Edit Rekening: {{ $acc->name }}</h3>
                        <button type="button" class="size-8 inline-flex justify-center items-center rounded-full border border-white/40 bg-white/60 text-gray-800 hover:bg-white/90 focus:outline-none transition shadow-2xs" data-hs-overlay="#hs-edit-account-modal-{{ $acc->id }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('accounts.update', $acc->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="p-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Nama Rekening / Dompet</label>
                                <input type="text" name="name" value="{{ $acc->name }}" required class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Jenis Rekening</label>
                                <select name="type" required class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                                    <option value="bank" {{ $acc->type === 'bank' ? 'selected' : '' }}>Bank 🏦</option>
                                    <option value="ewallet" {{ $acc->type === 'ewallet' ? 'selected' : '' }}>E-Wallet 📱</option>
                                    <option value="cash" {{ $acc->type === 'cash' ? 'selected' : '' }}>Tunai / Cash 💵</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Nomor Rekening / Akun (Opsional)</label>
                                <input type="text" name="account_number" value="{{ $acc->account_number }}" placeholder="Misal: 1234567890" class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Saldo Awal (Rp)</label>
                                <input type="text" name="initial_balance" value="{{ number_format($acc->initial_balance, 0, '', '') }}" data-currency-input required class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                            </div>
                        </div>

                        <div class="flex justify-end gap-x-2 py-3 px-4 modal-glass-footer">
                            <button type="button" class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-edit-account-modal-{{ $acc->id }}">Batal</button>
                            <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-[#66BB6A] text-white hover:bg-[#52A456] transition shadow-sm">Simpan Perubahan</button>
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
            <div class="w-full flex flex-col modal-glass rounded-2xl pointer-events-auto overflow-hidden">
                <div class="flex justify-between items-center py-3.5 px-4 modal-glass-header">
                    <h3 class="font-bold text-gray-900">Tambah Rekening Baru</h3>
                    <button type="button" class="size-8 inline-flex justify-center items-center rounded-full border border-white/40 bg-white/60 text-gray-800 hover:bg-white/90 focus:outline-none transition shadow-2xs" data-hs-overlay="#hs-add-account-modal">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('accounts.store') }}" method="POST">
                    @csrf
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Nama Rekening / Dompet</label>
                            <input type="text" name="name" required placeholder="Misal: Bank Mandiri / OVO" class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Jenis Rekening</label>
                            <select name="type" required class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                                <option value="bank">Bank 🏦</option>
                                <option value="ewallet">E-Wallet 📱</option>
                                <option value="cash">Tunai / Cash 💵</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Nomor Rekening / Akun (Opsional)</label>
                            <input type="text" name="account_number" placeholder="Misal: 9876543210" class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Saldo Awal (Rp)</label>
                            <input type="text" name="initial_balance" value="0" data-currency-input required placeholder="0" class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        </div>
                    </div>

                    <div class="flex justify-end gap-x-2 py-3 px-4 modal-glass-footer">
                        <button type="button" class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-add-account-modal">Batal</button>
                        <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-[#66BB6A] text-white hover:bg-[#52A456] transition shadow-sm">Simpan Rekening</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
