@extends('layouts.app')

@section('title', 'Dashboard Keuangan - Fibud')

@section('content')
<div class="space-y-6">

    <!-- HISTORICAL PERIOD SELECTOR BAR -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-4 sm:p-5 rounded-xl shadow-md border-none">
        <div class="flex items-center gap-x-3">
            <span class="inline-flex items-center justify-center size-11 rounded-xl bg-emerald-50 border border-emerald-100 shrink-0">
                <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4C5.svg" alt="Periode" class="size-7 shrink-0" />
            </span>
            <div>
                <div class="flex items-center gap-x-2">
                    <h1 class="text-lg sm:text-xl font-extrabold text-gray-900">Ringkasan Keuangan Dashboard</h1>
                    @if($selectedMonth !== $currentMonthReal)
                        <span class="inline-flex items-center gap-x-1 py-0.5 px-2.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            <span>📜 Masa Lampau:</span>
                            <span>{{ $selectedMonthLabel }}</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-x-1 py-0.5 px-2.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span>⚡ Periode Aktif:</span>
                            <span>{{ $selectedMonthLabel }}</span>
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Pilih periode bulan dan tahun untuk melihat catatan historis transaksi masa lampau</p>
            </div>
        </div>

        <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-x-2">
                <label for="period_select" class="text-xs font-semibold text-gray-600 whitespace-nowrap">Filter Periode:</label>
                <select id="period_select" name="period" onchange="this.form.submit()" class="py-2 px-3 bg-gray-50 border border-gray-300 rounded-xl text-xs font-bold text-gray-900 focus:bg-white focus:border-[#66BB6A] focus:ring-[#66BB6A] shadow-xs cursor-pointer">
                    @foreach($availableMonths as $mKey)
                        @php
                            $mLabel = \Carbon\Carbon::createFromFormat('Y-m', $mKey)->isoFormat('MMMM Y');
                        @endphp
                        <option value="{{ $mKey }}" {{ $selectedMonth === $mKey ? 'selected' : '' }}>
                            {{ $mLabel }} {{ $mKey === $currentMonthReal ? '(Bulan Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($selectedMonth !== $currentMonthReal)
            <a href="{{ route('dashboard') }}" class="py-2 px-3 inline-flex items-center gap-x-1 text-xs font-bold rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition shadow-xs">
                <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F504.svg" alt="Reset" class="size-3.5 shrink-0" />
                Kembali ke Bulan Ini
            </a>
            @endif
        </form>
    </div>

    <!-- Top Summary Cards (Custom Redesign: Card 1 White, Card 2 Emerald, Card 3 Red Rose, Card 4 White) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Saldo (Semua Rekening) -->
        <div class="bg-white rounded-xl p-4 shadow-md border-none">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Saldo (Semua Rekening)</span>
                <span class="inline-flex justify-center items-center size-9 rounded-xl bg-emerald-50 border border-emerald-100">
                    <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4B3.svg" alt="Total Saldo" class="size-6 shrink-0" />
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-x-2">
                <h3 class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
            </div>
            <p class="text-xs text-gray-500 mt-1">Gabungan sisa dana dari {{ count($accounts) }} dompet/rekening</p>
        </div>

        <!-- 2. Pemasukan Periode Selected (Hijau Emerald, Teks Putih) -->
        <div class="bg-[#10B981] rounded-xl p-4 shadow-md border-none text-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Pemasukan ({{ $selectedMonthLabel }})</span>
                <span class="inline-flex justify-center items-center size-9 rounded-xl bg-white/20">
                    <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4B5.svg" alt="Pemasukan" class="size-6 shrink-0" />
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-x-2">
                <h3 class="text-2xl font-extrabold text-white">+Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</h3>
            </div>
            <div class="flex items-center justify-between mt-1 text-xs text-white/90">
                <span>Pemasukan {{ $selectedMonthLabel }}</span>
                @if($incomeChange !== null)
                    <span class="font-bold bg-white/20 px-1.5 py-0.5 rounded text-[11px]">
                        {{ $incomeChange >= 0 ? '▲ +' : '▼ ' }}{{ $incomeChange }}% vs {{ $prevMonthLabel }}
                    </span>
                @endif
            </div>
        </div>

        <!-- 3. Pengeluaran Periode Selected (Red Rose, Teks Putih) -->
        <div class="bg-[#F43F5E] rounded-xl p-4 shadow-md border-none text-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Pengeluaran ({{ $selectedMonthLabel }})</span>
                <span class="inline-flex justify-center items-center size-9 rounded-xl bg-white/20">
                    <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/1F4B8.svg" alt="Pengeluaran" class="size-6 shrink-0" />
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-x-2">
                <h3 class="text-2xl font-extrabold text-white">-Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</h3>
            </div>
            <div class="flex items-center justify-between mt-1 text-xs text-white/90">
                <span>Pengeluaran {{ $selectedMonthLabel }}</span>
                @if($expenseChange !== null)
                    <span class="font-bold bg-white/20 px-1.5 py-0.5 rounded text-[11px]">
                        {{ $expenseChange >= 0 ? '▲ +' : '▼ ' }}{{ $expenseChange }}% vs {{ $prevMonthLabel }}
                    </span>
                @endif
            </div>
        </div>

        <!-- 4. Arus Kas Bersih (NET) Periode Selected (Background Putih, Teks Dinamis) -->
        <div class="bg-white rounded-xl p-4 shadow-md border-none">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Arus Kas Bersih ({{ $selectedMonthLabel }})</span>
                <span class="inline-flex justify-center items-center size-9 rounded-xl {{ $netFlow > 0 ? 'bg-emerald-50 border border-emerald-100' : ($netFlow < 0 ? 'bg-rose-50 border border-rose-100' : 'bg-gray-100 border border-gray-200') }}">
                    <img src="https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/2696.svg" alt="Arus Kas Net" class="size-6 shrink-0" />
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-x-2">
                <h3 class="text-2xl font-extrabold {{ $netFlow > 0 ? 'text-[#10B981]' : ($netFlow < 0 ? 'text-[#F43F5E]' : 'text-gray-900') }}">
                    {{ $netFlow > 0 ? '+' : ($netFlow < 0 ? '-' : '') }}Rp {{ number_format(abs($netFlow), 0, ',', '.') }}
                </h3>
            </div>
            <p class="text-xs text-gray-500 mt-1">Selisih pemasukan - pengeluaran {{ $selectedMonthLabel }}</p>
        </div>
    </div>

    <!-- REKENING / DOMPET QUICK OVERVIEW -->
    <div class="bg-white rounded-xl p-5 shadow-none border-none">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-gray-900">Rincian Saldo Per Rekening</h2>
                <p class="text-xs text-gray-500">Pantau saldo aktual di setiap rekening atau dompet digital Anda</p>
            </div>
            <a href="{{ route('accounts.index') }}" class="text-xs font-bold text-gray-900 hover:text-gray-700 transition">
                Kelola Rekening →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($accounts as $acc)
            <div class="p-3.5 bg-white rounded-xl shadow-md border-none">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-x-2">
                        <img src="{{ $acc->openmoji_icon_url }}" alt="{{ $acc->type_label }}" class="size-5 shrink-0" />
                        <span class="text-xs font-semibold text-gray-800">{{ $acc->name }}</span>
                    </div>
                    <span class="text-[11px] text-gray-400 font-medium">{{ $acc->account_number ?? 'Utama' }}</span>
                </div>
                <div class="mt-2 text-lg font-bold text-gray-900">
                    Rp {{ number_format($acc->balance, 0, ',', '.') }}
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- CHARTS SECTION GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Line Chart: Monthly Trend -->
        <div class="lg:col-span-2 bg-white rounded-xl p-5 shadow-md border-none">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Tren Keuangan 6 Bulan</h2>
                    <p class="text-xs text-gray-500">Perbandingan 6 bulan tren historis hingga {{ $selectedMonthLabel }}</p>
                </div>
            </div>
            <div id="monthly-trend-chart" class="min-h-[300px]"></div>
        </div>

        <!-- Donut Chart: Expense Breakdown -->
        <div class="bg-white rounded-xl p-5 shadow-md border-none">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Proporsi Pengeluaran</h2>
                    <p class="text-xs text-gray-500">Berdasarkan kategori periode {{ $selectedMonthLabel }}</p>
                </div>
            </div>
            <div id="expense-breakdown-chart" class="min-h-[300px] flex items-center justify-center"></div>
        </div>
    </div>

    <!-- BOTTOM GRID: BUDGETING PROGRESS & RECENT TRANSACTIONS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Budgeting Progress List -->
        <div class="bg-white rounded-xl p-5 shadow-md border-none">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Status Batas Anggaran (Budget)</h2>
                    <p class="text-xs text-gray-500">Penggunaan kuota anggaran periode {{ $selectedMonthLabel }}</p>
                </div>
                <a href="{{ route('budgets.index', ['month' => $selectedMonth]) }}" class="text-xs font-bold text-gray-900 hover:text-gray-700 transition">
                    Atur Budget →
                </a>
            </div>

            <div class="space-y-4">
                @forelse($budgetProgress as $budget)
                <div class="p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-center text-xs mb-1.5">
                        <span class="font-semibold text-gray-900">{{ $budget['category_name'] }}</span>
                        <span class="text-gray-500 font-medium">
                            Rp {{ number_format($budget['spent'], 0, ',', '.') }} / <span class="text-gray-900 font-bold">Rp {{ number_format($budget['limit'], 0, ',', '.') }}</span>
                        </span>
                    </div>

                    <!-- Progress Bar dengan Status Warna: Red Over, Amber Warning >= 80%, Green Normal -->
                    <div class="flex w-full h-2.5 bg-gray-100 rounded-full overflow-hidden" role="progressbar" aria-valuenow="{{ $budget['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="flex flex-col justify-center rounded-full overflow-hidden text-xs text-white text-center whitespace-nowrap transition-all duration-500 {{ $budget['is_over'] ? 'bg-rose-600' : ($budget['percentage'] >= 80 ? 'bg-amber-500' : 'bg-[#66BB6A]') }}" style="width: {{ min(100, $budget['percentage']) }}%"></div>
                    </div>

                    <div class="flex justify-between items-center text-[11px] mt-1 text-gray-500">
                        <span class="{{ $budget['is_over'] ? 'text-rose-600 font-bold' : ($budget['percentage'] >= 80 ? 'text-amber-700 font-bold' : 'text-emerald-600 font-medium') }}">
                            {{ $budget['is_over'] ? 'Melebihi Batas Anggaran!' : ($budget['percentage'] >= 80 ? 'Mendekati Kuota (' . $budget['percentage'] . '%)' : $budget['percentage'] . '% terpakai') }}
                        </span>
                        <span>Sisa: <strong class="text-gray-900 font-bold">Rp {{ number_format($budget['remaining'], 0, ',', '.') }}</strong></span>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-xs text-gray-500">
                    Belum ada batas anggaran diset untuk periode {{ $selectedMonthLabel }}.
                    <br>
                    <a href="{{ route('budgets.index', ['month' => $selectedMonth]) }}" class="text-gray-900 font-bold underline mt-1 inline-block">Set Budget Periode Ini</a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent / Historical Transactions for Selected Month -->
        <div class="bg-white rounded-xl p-5 shadow-md border-none">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Ringkasan Transaksi Periode Ini</h2>
                    <p class="text-xs text-gray-500">Daftar transaksi tercatat periode {{ $selectedMonthLabel }}</p>
                </div>
                <a href="{{ route('transactions.index', ['month' => $selectedMonth]) }}" class="text-xs font-bold text-gray-900 hover:text-gray-700 transition">
                    Lihat Semua →
                </a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentTransactions as $tx)
                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-x-3">
                        <div class="size-9 rounded-full flex items-center justify-center bg-gray-50 border border-gray-200 shrink-0">
                            <span class="size-3 rounded-full {{ $tx->category->type === 'income' ? 'bg-[#10B981]' : 'bg-[#F43F5E]' }}"></span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-900">{{ $tx->description ?? $tx->category->name }}</p>
                            <div class="flex items-center gap-x-2 text-[11px] text-gray-500 mt-0.5">
                                <span>{{ $tx->date->format('d M Y') }}</span>
                                <span>•</span>
                                <span>{{ $tx->account->name ?? 'Default' }}</span>
                                <span>•</span>
                                <span class="font-medium text-gray-800 flex items-center gap-x-1">
                                    {!! \App\Helpers\OpenMojiHelper::render($tx->category->icon_or_default, 'size-4 inline-block') !!}
                                    <span>{{ $tx->category->name }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="text-xs font-extrabold {{ $tx->category->type === 'income' ? 'text-emerald-600' : 'text-gray-900' }}">
                            {{ $tx->category->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-xs text-gray-500">
                    Belum ada transaksi recorded pada periode {{ $selectedMonthLabel }}.
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- APEXCHARTS LIBRARY & INITIALIZATION WITH GREEN ACCENT -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Line Chart: Monthly Trend
        const trendData = @json($monthlyTrend);
        const months = trendData.map(item => item.month);
        const incomes = trendData.map(item => item.income);
        const expenses = trendData.map(item => item.expense);

        const trendOptions = {
            series: [
                { name: 'Pemasukan', data: incomes },
                { name: 'Pengeluaran', data: expenses }
            ],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#10B981', '#F43F5E'], // Emerald Green & Rose Red
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: months,
                labels: { style: { fontSize: '11px', colors: '#4B5563' } }
            },
            yaxis: {
                labels: {
                    style: { fontSize: '11px', colors: '#4B5563' },
                    formatter: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val)
                }
            },
            tooltip: {
                y: { formatter: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
            },
            grid: { borderColor: '#C8E6C9' }
        };

        const trendChart = new ApexCharts(document.querySelector("#monthly-trend-chart"), trendOptions);
        trendChart.render();

        // 2. Donut Chart: Expense Breakdown
        const breakdownData = @json($expenseBreakdown);
        const breakdownLabels = breakdownData.map(item => item.name);
        const breakdownValues = breakdownData.map(item => parseFloat(item.total));

        const breakdownOptions = {
            series: breakdownValues.length > 0 ? breakdownValues : [1],
            labels: breakdownLabels.length > 0 ? breakdownLabels : ['Belum ada pengeluaran'],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#66BB6A', '#F43F5E', '#F59E0B', '#10B981', '#06B6D4', '#8B5CF6', '#EC4899'],
            legend: { position: 'bottom', fontSize: '12px' },
            dataLabels: { enabled: false },
            tooltip: {
                y: { formatter: (val) => 'Rp ' + new Intl.NumberFormat('id-ID').format(val) }
            }
        };

        const breakdownChart = new ApexCharts(document.querySelector("#expense-breakdown-chart"), breakdownOptions);
        breakdownChart.render();
    });
</script>
@endsection
