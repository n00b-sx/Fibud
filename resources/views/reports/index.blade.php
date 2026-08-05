@extends('layouts.app')

@section('title', 'Laporan Keuangan - Fibud')

@section('content')
<div class="space-y-6">

    <!-- Header Section & Download PDF Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Laporan Keuangan</h1>
            <p class="text-xs sm:text-sm text-gray-500">Rekapitulasi analisis pengeluaran, pemasukan, dan detail struk belanja ({{ $periodLabel }})</p>
        </div>
        <div class="flex items-center gap-x-2">
            <!-- PDF Download Button Only -->
            <a href="{{ route('reports.export-pdf', request()->query()) }}" target="_blank" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-[#66BB6A] text-white hover:bg-[#52A456] shadow-sm transition">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Unduh Laporan PDF
            </a>
        </div>
    </div>

    <!-- FILTER BAR REPORT (WHITE BG, SHADOW-MD, NO BORDER GRAY) -->
    <div class="bg-white rounded-xl p-4 shadow-md border-none">
        <form action="{{ route('reports.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Periode Cepat -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Periode Cepat</label>
                    <select name="period" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        <option value="this_month" {{ request('period', 'this_month') === 'this_month' ? 'selected' : '' }}>Bulan Ini ({{ date('M Y') }})</option>
                        <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="this_week" {{ request('period') === 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="this_year" {{ request('period') === 'this_year' ? 'selected' : '' }}>Tahun Ini ({{ date('Y') }})</option>
                        <option value="all" {{ request('period') === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                        <option value="custom" {{ request('start_date') || request('month_year') ? 'selected' : '' }}>Custom / Rentang Tanggal</option>
                    </select>
                </div>

                <!-- Filter Bulan & Tahun (Month Picker) -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Bulan & Tahun Spesifik</label>
                    <input type="month" name="month_year" value="{{ request('month_year') }}" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                </div>

                <!-- Dari Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal (Custom)</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                </div>

                <!-- Sampai Tanggal -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal (Custom)</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-100">
                <div>
                    <select name="account_id" class="py-2 px-3 block w-full bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A]">
                        <option value="">-- Semua Rekening --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                        @endforeach
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
                        Terapkan Filter Laporan
                    </button>
                    <a href="{{ route('reports.index') }}" class="py-2 px-3 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 flex items-center justify-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- SUMMARY CARDS (EXECUTIVE SUMMARY) -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <!-- Total Pemasukan -->
        <div class="bg-[#10B981] rounded-xl p-4 shadow-md border-none text-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Total Pemasukan</span>
                <span class="inline-flex justify-center items-center size-9 rounded-xl bg-white/20">
                    <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4B5.svg" alt="Pemasukan" class="size-6 shrink-0" />
                </span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-white">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            <p class="text-xs text-white/90 mt-1">Uang masuk periode laporan</p>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-[#F43F5E] rounded-xl p-4 shadow-md border-none text-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Total Pengeluaran</span>
                <span class="inline-flex justify-center items-center size-9 rounded-xl bg-white/20">
                    <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4B8.svg" alt="Pengeluaran" class="size-6 shrink-0" />
                </span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-white">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            <p class="text-xs text-white/90 mt-1">Total pengeluaran & belanja</p>
        </div>

        <!-- Arus Kas Bersih -->
        <div class="bg-white rounded-xl p-4 shadow-md border-none">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Arus Kas Bersih (Net)</span>
                <span class="inline-flex justify-center items-center size-9 rounded-xl {{ $netFlow > 0 ? 'bg-emerald-50 border border-emerald-100' : ($netFlow < 0 ? 'bg-rose-50 border border-rose-100' : 'bg-gray-100 border border-gray-200') }}">
                    <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/2696.svg" alt="Arus Kas Net" class="size-6 shrink-0" />
                </span>
            </div>
            <div class="mt-2 text-2xl font-extrabold {{ $netFlow > 0 ? 'text-[#10B981]' : ($netFlow < 0 ? 'text-[#F43F5E]' : 'text-gray-900') }}">
                {{ $netFlow > 0 ? '+' : ($netFlow < 0 ? '-' : '') }}Rp {{ number_format(abs($netFlow), 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-500 mt-1">Surplus / Defisit</p>
        </div>

        <!-- Jumlah Transaksi -->
        <div class="bg-white rounded-xl p-4 shadow-md border-none">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Jumlah Transaksi</span>
                <span class="inline-flex justify-center items-center size-9 rounded-xl bg-emerald-50 border border-emerald-100">
                    <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4DD.svg" alt="Jumlah Transaksi" class="size-6 shrink-0" />
                </span>
            </div>
            <div class="mt-2 text-2xl font-extrabold text-gray-900">{{ $transactions->count() }} Transaksi</div>
            <p class="text-xs text-gray-500 mt-1">Sesuai kriteria filter</p>
        </div>
    </div>

    <!-- REKAPITULASI PENGELUARAN PER KATEGORI -->
    <div class="bg-white rounded-xl p-5 shadow-md border-none space-y-4">
        <div>
            <h2 class="text-base font-bold text-gray-900">Rekapitulasi Pengeluaran Per Kategori</h2>
            <p class="text-xs text-gray-500">Rincian alokasi dana belanja dan persentase dari total pengeluaran</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($expenseCategoryBreakdown as $cat)
            <div class="p-3.5 bg-white border border-gray-100 rounded-xl space-y-2 shadow-xs">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-gray-900">{{ $cat['name'] }} <span class="font-normal text-gray-500">({{ $cat['count'] }}x)</span></span>
                    <span class="font-extrabold text-gray-900">Rp {{ number_format($cat['total'], 0, ',', '.') }}</span>
                </div>
                <div class="flex w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="bg-[#66BB6A] h-2 rounded-full" style="width: {{ $cat['percentage'] }}%"></div>
                </div>
                <div class="text-[11px] text-gray-500 text-end font-semibold">
                    {{ $cat['percentage'] }}% dari total pengeluaran
                </div>
            </div>
            @empty
            <div class="col-span-2 text-center py-4 text-xs text-gray-500">
                Tidak ada data pengeluaran pada periode ini.
            </div>
            @endforelse
        </div>
    </div>

    <!-- DETAIL TRANSAKSI & RINCIAN STRUK BELANJA TABLE -->
    <div class="bg-white rounded-xl overflow-hidden shadow-md border-none">
        <div class="p-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">Detail Transaksi & Struk Belanja</h2>
            <p class="text-xs text-gray-500">Daftar lengkap rincian per transaksi beserta barang belanjaan</p>
        </div>

        <div class="divide-y divide-gray-100 bg-white">
            @forelse($transactions as $tx)
            <div class="p-4 hover:bg-gray-50/60 transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-start gap-x-3">
                        <div class="size-9 rounded-full flex items-center justify-center bg-gray-50 border border-gray-200 shrink-0">
                            <span class="size-3 rounded-full {{ $tx->category->type === 'income' ? 'bg-[#10B981]' : 'bg-[#F43F5E]' }}"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-x-2">
                                <h3 class="text-sm font-bold text-gray-900">{{ $tx->description ?? $tx->category->name }}</h3>
                                <span class="py-0.5 px-2 rounded text-[11px] font-medium {{ $tx->category->type === 'income' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                    {{ $tx->category->name }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-3 text-xs text-gray-500 mt-1">
                                <span>📅 {{ $tx->date->format('d M Y') }}</span>
                                <span>💳 {{ $tx->account->name ?? 'Default' }}</span>
                                @if($tx->source_destination)
                                    <span class="font-semibold text-gray-700">
                                        {{ $tx->category->type === 'income' ? 'Dari: ' : 'Ke: ' }} {{ $tx->source_destination }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-start sm:text-end">
                        <div class="text-sm font-extrabold {{ $tx->category->type === 'income' ? 'text-emerald-600' : 'text-gray-900' }}">
                            {{ $tx->category->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- SUB-TABLE RINCIAN STRUK BARANG JIKA ADA -->
                @if($tx->items->count() > 0)
                <div class="mt-3 pt-3 border-t border-gray-100 bg-gray-50/80 rounded-lg p-3">
                    <div class="text-xs font-bold text-gray-800 mb-2 flex items-center gap-x-1">
                        <svg class="size-3.5 text-[#66BB6A]" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1z"/><path d="M16 8h-6"/><path d="M16 12h-6"/></svg>
                        Rincian Struk Barang ({{ $tx->items->count() }} item):
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead>
                                <tr class="text-gray-500 font-semibold uppercase text-[10px]">
                                    <th class="py-1 text-start">Nama Barang</th>
                                    <th class="py-1 text-center">Qty</th>
                                    <th class="py-1 text-end">Harga Satuan</th>
                                    <th class="py-1 text-end">Diskon</th>
                                    <th class="py-1 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($tx->items as $item)
                                <tr>
                                    <td class="py-1 text-gray-800 font-medium">{{ $item->name }}</td>
                                    <td class="py-1 text-center text-gray-600">{{ $item->quantity }}</td>
                                    <td class="py-1 text-end text-gray-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="py-1 text-end text-rose-600 font-medium">{{ $item->discount > 0 ? '-Rp ' . number_format($item->discount, 0, ',', '.') : '-' }}</td>
                                    <td class="py-1 text-end font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
            @empty
            <div class="p-8 text-center text-gray-500 text-sm">
                Tidak ada data transaksi ditemukan untuk periode ini.
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
