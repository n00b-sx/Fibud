<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F3F4F6] font-sans antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pencatatan Keuangan & Budgeting')</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F3F4F6] text-gray-900 min-h-screen">

    <!-- ========== HEADER (30% WHITE STRUCTURE + SHADOW-SM) ========== -->
    <header class="sticky top-0 z-40 flex flex-wrap sm:justify-start sm:flex-nowrap w-full bg-white border-b border-gray-200 shadow-sm py-2.5 sm:py-3.5">
        <nav class="max-w-[85rem] w-full mx-auto px-4 sm:flex sm:items-center sm:justify-between" aria-label="Global">
            <div class="flex items-center justify-between w-full lg:w-auto">
                <!-- Mobile Navigation Toggle -->
                <button type="button" class="lg:hidden inline-flex justify-center items-center gap-x-2 rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none p-2" data-hs-overlay="#application-sidebar" aria-controls="application-sidebar" aria-label="Toggle navigation">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                </button>
                
                <!-- Brand Logo with 10% Warm Orange Accent -->
                <a class="flex items-center gap-x-2.5 font-bold text-xl text-gray-900" href="{{ route('dashboard') }}">
                    <div class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center font-extrabold text-lg shadow-sm">
                        F
                    </div>
                    <span class="tracking-tight">Fibud</span>
                </a>

                <div class="lg:hidden">
                    <button type="button" class="py-1.5 px-3 inline-flex items-center gap-x-2 text-xs font-medium rounded-lg border border-transparent bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm" data-hs-overlay="#hs-add-transaction-modal">
                        + Transaksi
                    </button>
                </div>
            </div>

            <!-- Header Quick Actions (10% Warm Orange Accent) -->
            <div class="hidden lg:flex items-center gap-x-3">
                <button type="button" class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-orange-500 text-white hover:bg-orange-600 focus:outline-none focus:bg-orange-600 transition shadow-sm" data-hs-overlay="#hs-add-transaction-modal">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Catat Transaksi
                </button>
            </div>
        </nav>
    </header>
    <!-- ========== END HEADER ========== -->

    <!-- ========== MAIN CONTENT (60% NEUTRAL CREME BACKGROUND #F3F4F6) ========== -->
    <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-6 py-6">

            <!-- Sidebar (30% White Structure + Shadow-SM) -->
            <div id="application-sidebar" class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full fixed top-0 start-0 bottom-0 z-60 w-64 bg-white border-e border-gray-200 overflow-y-auto lg:block lg:static lg:translate-x-0 lg:z-10 lg:w-64 lg:shrink-0 lg:rounded-xl lg:border p-4 shadow-sm transition-all duration-300">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 lg:hidden">
                    <span class="font-semibold text-gray-900">Menu Utama</span>
                    <button type="button" class="p-1 inline-flex justify-center items-center rounded-lg text-gray-500 hover:bg-gray-100" data-hs-overlay="#application-sidebar">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <nav class="space-y-1.5 w-full flex flex-col">
                    <a class="flex items-center gap-x-3 py-2.5 px-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-orange-600 font-semibold border-s-4 border-orange-500' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" href="{{ route('dashboard') }}">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                        Dashboard
                    </a>

                    <a class="flex items-center gap-x-3 py-2.5 px-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('transactions.*') ? 'bg-orange-50 text-orange-600 font-semibold border-s-4 border-orange-500' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" href="{{ route('transactions.index') }}">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8.3"/><path d="M21 3l-7 7"/><path d="M3 3l7 7"/></svg>
                        Transaksi
                    </a>

                    <a class="flex items-center gap-x-3 py-2.5 px-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('transfers.*') ? 'bg-orange-50 text-orange-600 font-semibold border-s-4 border-orange-500' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" href="{{ route('transfers.index') }}">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 3 4 4-4 4"/><path d="M20 7H4"/><path d="m8 21-4-4 4-4"/><path d="M4 17h16"/></svg>
                        Transfer & Top Up
                    </a>

                    <a class="flex items-center gap-x-3 py-2.5 px-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('budgets.*') ? 'bg-orange-50 text-orange-600 font-semibold border-s-4 border-orange-500' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" href="{{ route('budgets.index') }}">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a1 1 0 0 0 1-1v-3"/><path d="M18 12h.01"/><path d="M14 12a2 2 0 1 0 4 0 2 2 0 0 0-4 0z"/></svg>
                        Batas Anggaran
                    </a>

                    <a class="flex items-center gap-x-3 py-2.5 px-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('accounts.*') ? 'bg-orange-50 text-orange-600 font-semibold border-s-4 border-orange-500' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" href="{{ route('accounts.index') }}">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        Rekening & Dompet
                    </a>

                    <a class="flex items-center gap-x-3 py-2.5 px-3 rounded-lg text-sm font-medium transition {{ request()->routeIs('categories.*') ? 'bg-orange-50 text-orange-600 font-semibold border-s-4 border-orange-500' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" href="{{ route('categories.index') }}">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16"/><path d="m6 16 6-12 6 12"/></svg>
                        Kelola Kategori
                    </a>
                </nav>

                <div class="mt-8 pt-4 border-t border-gray-200">
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs font-semibold text-gray-800">Mode Lokal</p>
                        <p class="text-xs text-gray-500 mt-0.5">Single user tanpa autentikasi.</p>
                    </div>
                </div>
            </div>
            <!-- End Sidebar -->

            <!-- Main Content Body -->
            <main class="flex-1 w-full min-w-0">
                <!-- Toast Alert Hijau Standard untuk Sukses -->
                @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 rounded-xl p-4 flex items-center justify-between shadow-sm" role="alert">
                    <div class="flex items-center gap-x-3">
                        <svg class="shrink-0 size-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                <!-- Toast Alert Merah Standard untuk Error -->
                @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-200 text-sm text-rose-800 rounded-xl p-4 shadow-sm">
                    <ul class="list-disc list-inside space-y-1 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- ========== GLOBAL MODAL: TAMBAH TRANSAKSI ========== -->
    <div id="hs-add-transaction-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
            <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
                <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">
                        Catat Transaksi / Struk Belanja Baru
                    </h3>
                    <button type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-none" data-hs-overlay="#hs-add-transaction-modal">
                        <span class="sr-only">Tutup</span>
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST" id="transaction-form">
                    @csrf
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="modal-account" class="block text-sm font-medium text-gray-900 mb-1">Rekening / Sumber Dana</label>
                                <select id="modal-account" name="account_id" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Rekening --</option>
                                    @php
                                        $allAccounts = \App\Models\Account::orderBy('name')->get();
                                    @endphp
                                    @foreach($allAccounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="modal-category" class="block text-sm font-medium text-gray-900 mb-1">Kategori Transaksi</label>
                                <select id="modal-category" name="category_id" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    @php
                                        $allCategories = \App\Models\Category::orderBy('type')->orderBy('name')->get();
                                    @endphp
                                    <optgroup label="--- PEMASUKAN ---">
                                        @foreach($allCategories->where('type', 'income') as $cat)
                                            <option value="{{ $cat->id }}">[Pemasukan] {{ $cat->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="--- PENGELUARAN ---">
                                        @foreach($allCategories->where('type', 'expense') as $cat)
                                            <option value="{{ $cat->id }}">[Pengeluaran] {{ $cat->name }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="modal-date" class="block text-sm font-medium text-gray-900 mb-1">Tanggal</label>
                                <input type="date" id="modal-date" name="date" value="{{ date('Y-m-d') }}" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label for="modal-amount" class="block text-sm font-medium text-gray-900 mb-1">Total Nominal Transaksi (Rp)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 start-0 flex items-center ps-3 text-gray-500 text-sm font-semibold">Rp</span>
                                    <input type="text" id="modal-amount" name="amount" data-currency-input placeholder="0" class="py-2 ps-9 pe-3 block w-full border-gray-200 rounded-lg text-sm font-bold focus:border-orange-500 focus:ring-orange-500">
                                </div>
                                <span class="text-[11px] text-gray-500">Format ribuan otomatis (cth: 10000 -> 10.000). Otomatis dihitung jika mengisi Struk.</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label id="modal-source-dest-label" for="modal-source-destination" class="block text-sm font-medium text-gray-900 mb-1">Sumber / Tujuan Dana</label>
                                <input type="text" id="modal-source-destination" name="source_destination" placeholder="Contoh: PT ABC (Sumber) / Indomaret (Tujuan)" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                            </div>

                            <div>
                                <label for="modal-description" class="block text-sm font-medium text-gray-900 mb-1">Keterangan / Catatan Toko</label>
                                <input type="text" id="modal-description" name="description" placeholder="Contoh: Belanja Bulanan di Indomaret" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                            </div>
                        </div>

                        <!-- SECTION: RINCIAN ITEM STRUK BELANJA -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Rincian Struk Barang (Opsional)</h4>
                                    <p class="text-[11px] text-gray-500">Isi jika ingin mencatat rincian per barang, harga satuan, dan diskon.</p>
                                </div>
                                <button type="button" id="btn-add-item" class="py-1 px-2.5 inline-flex items-center gap-x-1 text-xs font-semibold rounded-lg border border-orange-200 bg-orange-50 text-orange-700 hover:bg-orange-100 transition">
                                    + Tambah Barang
                                </button>
                            </div>

                            <div id="receipt-items-container" class="space-y-2">
                                <!-- Dynamic Item Rows -->
                            </div>
                        </div>

                    </div>

                    <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200">
                        <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-add-transaction-modal">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm">
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- GLOBAL JS FOR RUPIAH CURRENCY FORMATTING & LIVE CALCULATION -->
    <script>
        function formatRupiahString(val) {
            if (val === null || val === undefined || val === '') return '';
            let digits = val.toString().replace(/\D/g, '');
            if (!digits) return '';
            return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatRupiahString(val) {
            if (!val) return '';
            return val.toString().replace(/\D/g, '');
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Live formatting on typing for all [data-currency-input]
            document.addEventListener('input', function (e) {
                if (e.target && e.target.matches('[data-currency-input]')) {
                    let rawVal = e.target.value;
                    let formatted = formatRupiahString(rawVal);
                    e.target.value = formatted;
                }
            });

            // Unformat currency inputs on submit so backend gets clean numbers
            document.addEventListener('submit', function (e) {
                const form = e.target;
                form.querySelectorAll('[data-currency-input]').forEach(input => {
                    input.value = unformatRupiahString(input.value);
                });
            });

            // Format initial values
            document.querySelectorAll('[data-currency-input]').forEach(input => {
                if (input.value) {
                    input.value = formatRupiahString(input.value);
                }
            });

            // Dynamic Source / Destination Label for Global Modal
            const modalCatSelect = document.getElementById('modal-category');
            const modalSourceLabel = document.getElementById('modal-source-dest-label');
            const modalSourceInput = document.getElementById('modal-source-destination');

            if (modalCatSelect && modalSourceLabel) {
                modalCatSelect.addEventListener('change', function () {
                    const selectedOpt = modalCatSelect.options[modalCatSelect.selectedIndex];
                    const labelText = selectedOpt.text;

                    if (labelText.includes('[Pemasukan]')) {
                        modalSourceLabel.textContent = 'Sumber Dana (Diterima Dari)';
                        if (modalSourceInput) modalSourceInput.placeholder = 'Contoh: PT ABC, Klien Budi, Hadiah';
                    } else if (labelText.includes('[Pengeluaran]')) {
                        modalSourceLabel.textContent = 'Tujuan Dana (Dibayarkan Ke)';
                        if (modalSourceInput) modalSourceInput.placeholder = 'Contoh: Indomaret, PLN, Tokopedia';
                    }
                });
            }

            // Handle Add Transaction Modal Items & Calculation
            const container = document.getElementById('receipt-items-container');
            const btnAdd = document.getElementById('btn-add-item');
            const inputTotalAmount = document.getElementById('modal-amount');
            let itemIndex = 0;

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

            function addReceiptRow(name = '', qty = 1, price = '', discount = '') {
                const row = document.createElement('div');
                row.className = 'receipt-item-row grid grid-cols-12 gap-2 items-center bg-gray-50 p-2 rounded-lg border border-gray-200';
                row.innerHTML = `
                    <div class="col-span-4 sm:col-span-4">
                        <input type="text" name="items[${itemIndex}][name]" value="${name}" placeholder="Nama barang (cth: Susu 1L)" required class="py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div class="col-span-2 sm:col-span-2">
                        <input type="number" name="items[${itemIndex}][quantity]" value="${qty}" min="1" placeholder="Qty" required class="item-qty py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div class="col-span-3 sm:col-span-3">
                        <input type="text" name="items[${itemIndex}][unit_price]" value="${price}" data-currency-input placeholder="Harga" required class="item-price py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div class="col-span-2 sm:col-span-2">
                        <input type="text" name="items[${itemIndex}][discount]" value="${discount}" data-currency-input placeholder="Diskon" class="item-discount py-1.5 px-2 block w-full border-gray-200 rounded-md text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div class="col-span-1 flex items-center justify-end">
                        <button type="button" class="btn-remove-item text-rose-500 hover:text-rose-700 p-1">
                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                    <div class="col-span-12 text-end text-[11px] text-gray-500 pt-1 border-t border-gray-200/50">
                        Subtotal: <span class="item-subtotal font-semibold text-gray-900">Rp 0</span>
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
            }

            if (btnAdd) {
                btnAdd.addEventListener('click', () => addReceiptRow());
            }
        });
    </script>

</body>
</html>
