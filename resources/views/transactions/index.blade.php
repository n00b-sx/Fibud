@extends('layouts.app')

@section('title', 'Catatan Transaksi - Fibud')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Catatan Transaksi</h1>
            <p class="text-xs sm:text-sm text-gray-600">Kelola riwayat pemasukan, pengeluaran, sumber/tujuan dana, dan rincian struk</p>
        </div>
        <div>
            <button type="button" class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#66BB6A] text-white hover:bg-[#52A456] shadow-sm transition" data-hs-overlay="#hs-add-transaction-modal">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Transaksi Baru
            </button>
        </div>
    </div>

    <!-- CARDS AKUMULASI HASIL FILTER (WHITE THEME SYSTEM) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Akumulasi Pemasukan Filtered (Emerald BG) -->
        <div class="bg-[#10B981] rounded-xl p-4 shadow-md border-none text-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Pemasukan (Hasil Filter)</span>
                <span class="inline-flex justify-center items-center size-8 rounded-lg bg-white/20 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M12 18H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5"/><path d="m16 19 3 3 3-3"/><path d="M18 12h.01"/><path d="M19 16v6"/><path d="M6 12h.01"/><circle cx="12" cy="12" r="2"/></svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-white">
                +Rp {{ number_format($filteredIncome, 0, ',', '.') }}
            </div>
            <p class="text-xs text-white/90 mt-1">Total akumulasi dari {{ $transactions->total() }} kriteria transaksi</p>
        </div>

        <!-- Akumulasi Pengeluaran Filtered (Rose BG) -->
        <div class="bg-[#F43F5E] rounded-xl p-4 shadow-md border-none text-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Pengeluaran (Hasil Filter)</span>
                <span class="inline-flex justify-center items-center size-8 rounded-lg bg-white/20 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M12 18H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5"/><path d="M18 12h.01"/><path d="M19 22v-6"/><path d="m22 19-3-3-3 3"/><path d="M6 12h.01"/><circle cx="12" cy="12" r="2"/></svg>                
                </span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-white">
                -Rp {{ number_format($filteredExpense, 0, ',', '.') }}
            </div>
            <p class="text-xs text-white/90 mt-1">Total pengeluaran tercatat</p>
        </div>

        <!-- Akumulasi Net Flow Filtered (White BG with Dynamic Text) -->
        <div class="bg-white rounded-xl p-4 shadow-md border-none">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Selisih Net (Hasil Filter)</span>
                <span class="inline-flex justify-center items-center size-8 rounded-lg {{ $filteredNet > 0 ? 'bg-emerald-100 text-emerald-700' : ($filteredNet < 0 ? 'bg-rose-100 text-rose-600' : 'bg-gray-100 text-gray-700') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M12 3v18"/><path d="m19 8 3 8a5 5 0 0 1-6 0zV7"/><path d="M3 7h1a17 17 0 0 0 8-2 17 17 0 0 0 8 2h1"/><path d="m5 8 3 8a5 5 0 0 1-6 0zV7"/><path d="M7 21h10"/></svg>
                </span>
            </div>
            <div class="mt-2 text-2xl font-extrabold {{ $filteredNet > 0 ? 'text-[#10B981]' : ($filteredNet < 0 ? 'text-[#F43F5E]' : 'text-gray-900') }}">
                {{ $filteredNet > 0 ? '+' : ($filteredNet < 0 ? '-' : '') }}Rp {{ number_format(abs($filteredNet), 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-500 mt-1">Surplus / Defisit hasil filter</p>
        </div>
    </div>

    <!-- FILTER BAR COMPREHENSIVE CARD -->
    <div class="bg-white rounded-xl p-4 shadow-md border-none">
        <form action="{{ route('transactions.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search Text -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Cari Kata Kunci</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari keterangan, sumber, barang..." class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                </div>

                <!-- Filter Periode Waktu -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Periode Waktu Cepat</label>
                    <select name="period" id="filter-period" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        <option value="">-- Semua Waktu --</option>
                        <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="this_week" {{ request('period') === 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="this_month" {{ request('period') === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ request('period') === 'custom' || request('start_date') ? 'selected' : '' }}>Rentang Tanggal Custom</option>
                    </select>
                </div>

                <!-- Filter Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                </div>

                <!-- Filter Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                </div>

                <!-- Filter Rekening -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Sumber Rekening</label>
                    <select name="account_id" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        <option value="">-- Semua Rekening --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-100">
                <div>
                    <select name="type" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        <option value="">-- Semua Tipe (Pemasukan / Pengeluaran) --</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <select name="category_id" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->icon_or_default }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="w-full py-2 px-3 bg-white border border-gray-200 text-gray-800 rounded-lg text-xs font-semibold hover:bg-gray-50 shadow-xs transition">
                        Terapkan Filter
                    </button>
                    @if(request()->hasAny(['search', 'type', 'category_id', 'account_id', 'period', 'start_date', 'end_date']))
                    <a href="{{ route('transactions.index') }}" class="py-2 px-3 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 flex items-center justify-center">
                        Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- TRANSACTIONS TABLE CARD -->
    <div class="bg-white rounded-xl overflow-hidden shadow-md border-none">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-xs font-semibold text-gray-500 uppercase">
                        <th class="py-3 px-4 text-start">Tanggal</th>
                        <th class="py-3 px-4 text-start">Rekening</th>
                        <th class="py-3 px-4 text-center">Kategori</th>
                        <th class="py-3 px-4 text-start">Sumber / Tujuan Dana</th>
                        <th class="py-3 px-4 text-start">Keterangan / Struk</th>
                        <th class="py-3 px-4 text-end">Nominal</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white text-sm">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="py-3 px-4 whitespace-nowrap text-gray-600 text-xs sm:text-sm font-medium">
                            {{ $tx->date->format('d M Y') }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap text-xs font-semibold text-gray-800">
                            {{ $tx->account->name ?? 'Default' }}
                        </td>
                        <!-- COLUMN: EMOJI BADGE ONLY WITH HOVER TOOLTIP -->
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center text-base transition hover:scale-110 cursor-help" title="{{ $tx->category->name }} ({{ $tx->category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }})">
                                {!! \App\Helpers\OpenMojiHelper::render($tx->category->icon_or_default, 'size-6') !!}
                            </span>
                        </td>
                        <!-- COLUMN: SUMBER / TUJUAN DANA -->
                        <td class="py-3 px-4 text-xs">
                            @if($tx->source_destination)
                                <div class="font-semibold text-gray-800 flex items-center gap-x-1">
                                    @if($tx->category->type === 'income')
                                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Dari:</span>
                                    @else
                                        <span class="text-[11px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">Ke:</span>
                                    @endif
                                    <span>{{ $tx->source_destination }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-gray-800 text-xs sm:text-sm">
                            <div class="font-medium">{{ $tx->description ?? '-' }}</div>
                            @if($tx->items->count() > 0)
                            <div class="mt-1">
                                <button type="button" class="inline-flex items-center gap-x-1 py-0.5 px-2 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold hover:bg-emerald-100 transition" data-hs-overlay="#hs-receipt-modal-{{ $tx->id }}">
                                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1z"/><path d="M16 8h-6"/><path d="M16 12h-6"/><path d="M16 16h-6"/></svg>
                                    Lihat Struk ({{ $tx->items->count() }} barang)
                                </button>
                            </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end font-extrabold whitespace-nowrap {{ $tx->category->type === 'income' ? 'text-emerald-600' : 'text-gray-900' }}">
                            {{ $tx->category->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-x-2">
                                <button type="button" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 py-1 px-2 rounded-lg hover:bg-emerald-50 transition" data-hs-overlay="#hs-edit-transaction-modal-{{ $tx->id }}">
                                    Ubah
                                </button>
                                <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 py-1 px-2 rounded-lg hover:bg-rose-50 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-500 text-sm">
                            Tidak ada data transaksi yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-gray-100 bg-white">
            {{ $transactions->appends(request()->query())->links('pagination::tailwind') }}
        </div>
        @endif
    </div>

</div>

<!-- MODAL DETAIL STRUK BELANJA & MODAL EDIT TRANSAKSI -->
@foreach($transactions as $tx)
<!-- 1. MODAL DETAIL STRUK -->
@if($tx->items->count() > 0)
<div id="hs-receipt-modal-{{ $tx->id }}" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Rincian Struk Belanja</h3>
                    <p class="text-xs text-gray-500">{{ $tx->description ?? 'Transaksi Belanja' }} • {{ $tx->date->format('d M Y') }}</p>
                </div>
                <button type="button" class="size-8 inline-flex justify-center items-center rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" data-hs-overlay="#hs-receipt-modal-{{ $tx->id }}">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <div class="p-4 sm:p-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                    <thead>
                        <tr class="text-gray-500 font-semibold uppercase text-xs">
                            <th class="py-2.5 px-3 text-start">Barang</th>
                            <th class="py-2.5 px-3 text-center whitespace-nowrap">Qty</th>
                            <th class="py-2.5 px-3 text-end whitespace-nowrap">Harga Satuan</th>
                            <th class="py-2.5 px-3 text-end whitespace-nowrap">Diskon</th>
                            <th class="py-2.5 px-3 text-end whitespace-nowrap">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($tx->items as $item)
                        <tr>
                            <td class="py-2.5 px-3 text-gray-900 font-medium">{{ $item->name }}</td>
                            <td class="py-2.5 px-3 text-center text-gray-600 font-medium whitespace-nowrap">{{ $item->quantity }}</td>
                            <td class="py-2.5 px-3 text-end text-gray-600 whitespace-nowrap">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-end text-rose-600 font-medium whitespace-nowrap">{{ $item->discount > 0 ? '-Rp ' . number_format($item->discount, 0, ',', '.') : '-' }}</td>
                            <td class="py-2.5 px-3 text-end font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-300 font-bold">
                            <td colspan="4" class="py-2.5 text-end text-gray-900">Total Struk:</td>
                            <td class="py-2.5 text-end text-gray-900 text-sm font-extrabold">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex justify-end py-3 px-4 border-t border-gray-200">
                <button type="button" class="py-2 px-3 text-xs font-medium rounded-lg border border-gray-200 bg-white text-gray-800" data-hs-overlay="#hs-receipt-modal-{{ $tx->id }}">Tutup Struk</button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- 2. MODAL EDIT TRANSAKSI -->
<div id="hs-edit-transaction-modal-{{ $tx->id }}" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">
                    Ubah Transaksi
                </h3>
                <button type="button" class="size-8 inline-flex justify-center items-center rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" data-hs-overlay="#hs-edit-transaction-modal-{{ $tx->id }}">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('transactions.update', $tx->id) }}" method="POST" class="edit-transaction-form" data-tx-id="{{ $tx->id }}">
                @csrf
                @method('PUT')
                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Rekening / Sumber Dana</label>
                            <select name="account_id" required class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ $tx->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Kategori Transaksi</label>
                            <select name="category_id" required class="edit-category-select py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500">
                                <optgroup label="--- PEMASUKAN ---">
                                    @foreach($categories->where('type', 'income') as $cat)
                                        <option value="{{ $cat->id }}" data-type="income" {{ $tx->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->icon_or_default }} {{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="--- PENGELUARAN ---">
                                    @foreach($categories->where('type', 'expense') as $cat)
                                        <option value="{{ $cat->id }}" data-type="expense" {{ $tx->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->icon_or_default }} {{ $cat->name }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Tanggal</label>
                            <input type="date" name="date" value="{{ $tx->date->format('Y-m-d') }}" required class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Total Nominal Transaksi (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-gray-500 text-sm font-semibold">Rp</span>
                                <input type="text" name="amount" value="{{ number_format($tx->amount, 0, '', '') }}" data-currency-input placeholder="0" class="edit-amount-input py-2 ps-9 pe-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold focus:bg-white focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="edit-source-dest-label block text-sm font-medium text-gray-900 mb-1">
                                {{ $tx->category->type === 'income' ? 'Sumber Dana (Diterima Dari)' : 'Tujuan Dana (Dibayarkan Ke)' }}
                            </label>
                            <input type="text" name="source_destination" value="{{ $tx->source_destination }}" placeholder="Contoh: PT ABC / Indomaret" class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-1">Keterangan / Catatan Toko</label>
                            <input type="text" name="description" value="{{ $tx->description }}" placeholder="Keterangan transaksi" class="py-2 px-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm focus:bg-white focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- SECTION: EDIT RINCIAN ITEM STRUK BELANJA -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Rincian Struk Barang</h4>
                                <p class="text-[11px] text-gray-500">Edit item barang, harga satuan, dan diskon.</p>
                            </div>
                            <button type="button" class="btn-edit-add-item py-1 px-2.5 inline-flex items-center gap-x-1 text-xs font-semibold rounded-lg border border-orange-200 bg-orange-50 text-orange-700 hover:bg-orange-100 transition">
                                + Tambah Barang
                            </button>
                        </div>

                        <div class="edit-items-container space-y-2">
                            @foreach($tx->items as $idx => $item)
                            <div class="receipt-item-row grid grid-cols-12 gap-2 items-center bg-gray-50 p-2 rounded-lg border border-gray-200">
                                <div class="col-span-4 sm:col-span-4">
                                    <input type="text" name="items[{{ $idx }}][name]" value="{{ $item->name }}" placeholder="Nama barang" required class="py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div class="col-span-2 sm:col-span-2">
                                    <input type="number" name="items[{{ $idx }}][quantity]" value="{{ $item->quantity }}" min="1" placeholder="Qty" required class="item-qty py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div class="col-span-3 sm:col-span-3">
                                    <input type="text" name="items[{{ $idx }}][unit_price]" value="{{ number_format($item->unit_price, 0, '', '') }}" data-currency-input placeholder="Harga" required class="item-price py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div class="col-span-2 sm:col-span-2">
                                    <input type="text" name="items[{{ $idx }}][discount]" value="{{ number_format($item->discount, 0, '', '') }}" data-currency-input placeholder="Diskon" class="item-discount py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <div class="col-span-1 flex items-center justify-end">
                                    <button type="button" class="btn-remove-item text-rose-500 hover:text-rose-700 p-1">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>
                                </div>
                                <div class="col-span-12 text-end text-[11px] text-gray-500 pt-1 border-t border-gray-200/50">
                                    Subtotal: <span class="item-subtotal font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200">
                    <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-edit-transaction-modal-{{ $tx->id }}">
                        Batal
                    </button>
                    <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-[#66BB6A] text-white hover:bg-[#52A456] transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.edit-transaction-form').forEach(form => {
            const container = form.querySelector('.edit-items-container');
            const btnAdd = form.querySelector('.btn-edit-add-item');
            const inputTotalAmount = form.querySelector('.edit-amount-input');
            const catSelect = form.querySelector('.edit-category-select');
            const labelSourceDest = form.querySelector('.edit-source-dest-label');
            let itemIndex = container.querySelectorAll('.receipt-item-row').length;

            if (catSelect && labelSourceDest) {
                catSelect.addEventListener('change', function () {
                    const selectedOpt = catSelect.options[catSelect.selectedIndex];
                    const type = selectedOpt.getAttribute('data-type');
                    if (type === 'income') {
                        labelSourceDest.textContent = 'Sumber Dana (Diterima Dari)';
                    } else {
                        labelSourceDest.textContent = 'Tujuan Dana (Dibayarkan Ke)';
                    }
                });
            }

            function calculateTotals() {
                let grandTotal = 0;
                const rows = container.querySelectorAll('.receipt-item-row');
                
                rows.forEach(row => {
                    const qtyInput = row.querySelector('.item-qty');
                    const priceInput = row.querySelector('.item-price');
                    const discountInput = row.querySelector('.item-discount');
                    const subtotalEl = row.querySelector('.item-subtotal');

                    const qty = parseFloat(qtyInput.value) || 0;
                    const price = parseFloat(unformatRupiahString(priceInput.value)) || 0;
                    const discount = parseFloat(unformatRupiahString(discountInput.value)) || 0;

                    const subtotal = Math.max(0, (qty * price) - discount);
                    subtotalEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);

                    grandTotal += subtotal;
                });

                if (rows.length > 0) {
                    inputTotalAmount.value = formatRupiahString(grandTotal);
                }
            }

            container.querySelectorAll('.receipt-item-row').forEach(row => {
                row.querySelectorAll('.item-qty, .item-price, .item-discount').forEach(input => {
                    input.addEventListener('input', calculateTotals);
                });
                row.querySelector('.btn-remove-item').addEventListener('click', function () {
                    row.remove();
                    calculateTotals();
                });
            });

            if (btnAdd) {
                btnAdd.addEventListener('click', function () {
                    const row = document.createElement('div');
                    row.className = 'receipt-item-row grid grid-cols-12 gap-2 items-center bg-gray-50 p-2 rounded-lg border border-gray-200';
                    row.innerHTML = `
                        <div class="col-span-4 sm:col-span-4">
                            <input type="text" name="items[${itemIndex}][name]" placeholder="Nama barang" required class="py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div class="col-span-2 sm:col-span-2">
                            <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" placeholder="Qty" required class="item-qty py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div class="col-span-3 sm:col-span-3">
                            <input type="text" name="items[${itemIndex}][unit_price]" data-currency-input placeholder="Harga" required class="item-price py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div class="col-span-2 sm:col-span-2">
                            <input type="text" name="items[${itemIndex}][discount]" data-currency-input placeholder="Diskon" class="item-discount py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div class="col-span-1 flex items-center justify-end">
                            <button type="button" class="btn-remove-item text-rose-500 hover:text-rose-700 p-1">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                        <div class="col-span-12 text-end text-[11px] text-gray-500 pt-1 border-t border-gray-200/50">
                            Subtotal: <span class="item-subtotal font-bold text-gray-900">Rp 0</span>
                        </div>
                    `;

                    container.appendChild(row);
                    itemIndex++;

                    row.querySelectorAll('.item-qty, .item-price, .item-discount').forEach(input => {
                        input.addEventListener('input', calculateTotals);
                    });
                    row.querySelector('.btn-remove-item').addEventListener('click', function () {
                        row.remove();
                        calculateTotals();
                    });

                    calculateTotals();
                });
            }
        });
    </script>
@endsection
