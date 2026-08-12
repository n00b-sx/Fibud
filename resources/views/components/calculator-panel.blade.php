<!-- REUSABLE TRANSACTION MINI-CALCULATOR PANEL -->
<div class="transaction-calculator-panel hidden mb-4 p-4 bg-slate-900/95 backdrop-blur-md rounded-2xl text-white shadow-2xl border border-white/20">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-x-2">
            {!! \App\Helpers\OpenMojiHelper::render('🧮', 'size-5 shrink-0') !!}
            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-400">Kalkulator Hitung Transaksi</span>
        </div>
        <button type="button" class="btn-close-calculator text-slate-400 hover:text-white text-xs font-bold transition">
            ✕ Tutup Kalkulator
        </button>
    </div>

    <!-- Calculator Display Screen -->
    <div class="bg-slate-950 p-3 rounded-xl mb-3 text-end font-mono border border-slate-800 shadow-inner">
        <div class="calc-expression text-xs text-slate-400 min-h-[16px] overflow-hidden text-ellipsis whitespace-nowrap"></div>
        <div class="calc-result text-2xl font-extrabold text-emerald-400 min-h-[32px] tracking-tight">0</div>
    </div>

    <!-- Target Selection & Apply Button -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-3">
        <div class="flex items-center gap-x-2 flex-1">
            <span class="text-[11px] text-slate-300 font-semibold whitespace-nowrap">Tempel Ke:</span>
            <select class="calc-target-select flex-1 py-1.5 px-2 bg-slate-800 border border-slate-700 rounded-lg text-xs font-bold text-white focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer">
                <option value="amount">Nominal Total Transaksi</option>
                <option value="discount">Diskon Akhir Struk (Voucher)</option>
                <option value="item_price">Harga Satuan Barang Terakhir</option>
            </select>
        </div>
        <button type="button" class="btn-apply-calc-result py-1.5 px-3 bg-emerald-500 hover:bg-emerald-600 text-slate-950 text-xs font-extrabold rounded-lg transition shadow-md flex items-center justify-center gap-x-1">
            <span>✓ Tempel Hasil Hitung</span>
        </button>
    </div>

    <!-- Keypad Buttons Grid -->
    <div class="grid grid-cols-4 gap-1.5 font-bold text-sm">
        <button type="button" class="calc-btn py-2 bg-rose-500/80 hover:bg-rose-600 rounded-xl text-white shadow-2xs transition" data-action="clear">C</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-slate-200 shadow-2xs transition" data-op="(">(</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-slate-200 shadow-2xs transition" data-op=")">)</button>
        <button type="button" class="calc-btn py-2 bg-amber-500/80 hover:bg-amber-600 rounded-xl text-white shadow-2xs transition" data-op="/">÷</button>

        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="7">7</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="8">8</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="9">9</button>
        <button type="button" class="calc-btn py-2 bg-amber-500/80 hover:bg-amber-600 rounded-xl text-white shadow-2xs transition" data-op="*">×</button>

        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="4">4</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="5">5</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="6">6</button>
        <button type="button" class="calc-btn py-2 bg-amber-500/80 hover:bg-amber-600 rounded-xl text-white shadow-2xs transition" data-op="-">−</button>

        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="1">1</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="2">2</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="3">3</button>
        <button type="button" class="calc-btn py-2 bg-amber-500/80 hover:bg-amber-600 rounded-xl text-white shadow-2xs transition" data-op="+">+</button>

        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="0">0</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="00">00</button>
        <button type="button" class="calc-btn py-2 bg-slate-800 hover:bg-slate-700 rounded-xl text-white shadow-2xs transition" data-num="000">000</button>
        <button type="button" class="calc-btn py-2 bg-emerald-500 hover:bg-emerald-600 rounded-xl text-slate-950 font-black shadow-2xs transition" data-action="equals">=</button>
    </div>
</div>
