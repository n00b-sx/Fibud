@extends('layouts.app')

@section('title', 'Manajemen Budget - Fibud')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Batas Anggaran (Budgeting)</h1>
            <p class="text-xs sm:text-sm text-gray-500">Atur plafon maksimal pengeluaran bulanan per kategori</p>
        </div>

        <!-- Month Filter Picker -->
        <form action="{{ route('budgets.index') }}" method="GET" class="flex items-center gap-2">
            <input type="month" name="month_year" value="{{ $selectedMonth }}" onchange="this.form.submit()" class="py-2 px-3 block border-gray-200 rounded-lg text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500 font-semibold shadow-sm">
        </form>
    </div>

    <!-- BUDGET CARDS GRID (PURE WHITE CARDS + SHADOW-SM ON CREME BG) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($budgetsData as $item)
        <div class="flex flex-col bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">{{ $item['category_name'] }}</h3>
                    <span class="text-xs text-gray-500">Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->isoFormat('MMMM Y') }}</span>
                </div>
                <button type="button" class="py-1.5 px-3 inline-flex items-center gap-x-1.5 text-xs font-semibold rounded-lg border border-orange-200 bg-orange-50 text-orange-700 hover:bg-orange-100 transition shadow-sm" data-hs-overlay="#hs-set-budget-modal-{{ $item['category_id'] }}">
                    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"/></svg>
                    {{ $item['has_budget'] ? 'Edit' : 'Set Budget' }}
                </button>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-200 space-y-3">
                <div class="flex justify-between items-baseline text-xs">
                    <span class="text-gray-500">Terpakai:</span>
                    <span class="font-bold text-gray-900">Rp {{ number_format($item['spent'], 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-baseline text-xs">
                    <span class="text-gray-500">Batas Maksimal:</span>
                    <span class="font-semibold text-gray-800">
                        {{ $item['amount_limit'] > 0 ? 'Rp ' . number_format($item['amount_limit'], 0, ',', '.') : 'Belum diset' }}
                    </span>
                </div>

                <!-- PRELINE DEFAULT PROGRESS BAR WITH WARM ORANGE & ROSE STATUS -->
                @if($item['amount_limit'] > 0)
                <div>
                    <div class="flex justify-between items-center text-[11px] mb-1">
                        <span class="{{ $item['is_over'] ? 'text-rose-600 font-bold' : 'text-gray-600' }}">
                            {{ $item['is_over'] ? 'Melebihi Budget!' : 'Terpakai' }}
                        </span>
                        <span class="font-extrabold text-gray-900">{{ $item['percentage'] }}%</span>
                    </div>
                    <div class="flex w-full h-2.5 bg-gray-100 rounded-full overflow-hidden" role="progressbar" aria-valuenow="{{ $item['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="flex flex-col justify-center rounded-full overflow-hidden text-xs text-white text-center whitespace-nowrap transition-all duration-500 {{ $item['is_over'] ? 'bg-rose-600' : 'bg-orange-500' }}" style="width: {{ min(100, $item['percentage']) }}%"></div>
                    </div>
                </div>
                <p class="text-[11px] text-gray-500 text-end">Sisa: <span class="font-bold text-gray-900">Rp {{ number_format($item['remaining'], 0, ',', '.') }}</span></p>
                @else
                <div class="py-2 text-center text-xs text-gray-500 bg-gray-50 rounded-lg">
                    Klik 'Set Budget' untuk menentukan kuota.
                </div>
                @endif
            </div>
        </div>

        <!-- MODAL SET BUDGET PER CATEGORY -->
        <div id="hs-set-budget-modal-{{ $item['category_id'] }}" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
            <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
                <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
                    <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                        <h3 class="font-bold text-gray-900">Batas Budget: {{ $item['category_name'] }}</h3>
                        <button type="button" class="size-8 inline-flex justify-center items-center rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" data-hs-overlay="#hs-set-budget-modal-{{ $item['category_id'] }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('budgets.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $item['category_id'] }}">
                        <input type="hidden" name="month_year" value="{{ $selectedMonth }}">

                        <div class="p-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Bulan & Tahun</label>
                                <input type="text" readonly value="{{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->isoFormat('MMMM Y') }}" class="py-2 px-3 block w-full bg-gray-100 border-gray-200 rounded-lg text-sm font-semibold">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-1">Batas Anggaran (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-gray-500 text-sm font-semibold">Rp</span>
                                    <input type="text" name="amount_limit" value="{{ number_format($item['amount_limit'], 0, '', '') }}" data-currency-input required placeholder="Contoh: 1.500.000" class="py-2 ps-9 pe-3 block w-full bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold focus:bg-white focus:border-orange-500 focus:ring-orange-500">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-x-2 py-3 px-4 border-t border-gray-200">
                            <button type="button" class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-set-budget-modal-{{ $item['category_id'] }}">Batal</button>
                            <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
