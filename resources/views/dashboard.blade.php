@extends('layouts.app')

@section('title', 'Dashboard Keuangan - Fibud')

@section('content')
<div class="space-y-6">

    <!-- Top Summary Cards (Custom Redesign: Card 1 White, Card 2 Emerald, Card 3 Red Rose, Card 4 White) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Saldo (Semua Rekening) -->
        <div class="bg-white rounded-xl p-4 shadow-md border-none">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Saldo (Semua Rekening)</span>
                <span class="inline-flex justify-center items-center size-8 rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-x-2">
                <h3 class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
            </div>
            <p class="text-xs text-gray-500 mt-1">Gabungan sisa dana dari {{ count($accounts) }} dompet/rekening</p>
        </div>

        <!-- 2. Pemasukan Bulan Ini (Hijau Emerald, Teks Putih) -->
        <div class="bg-[#10B981] rounded-xl p-4 shadow-md border-none text-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Pemasukan Bulan Ini</span>
                <span class="inline-flex justify-center items-center size-8 rounded-lg bg-white/20 text-white">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="M11 4h10"/><path d="M11 8h7"/><path d="M11 12h4"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-x-2">
                <h3 class="text-2xl font-extrabold text-white">+Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</h3>
            </div>
            <p class="text-xs text-white/90 mt-1">Total pemasukan bulan {{ date('F Y') }}</p>
        </div>

        <!-- 3. Pengeluaran Bulan Ini (Red Rose, Teks Putih) -->
        <div class="bg-[#F43F5E] rounded-xl p-4 shadow-md border-none text-white">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-white/90">Pengeluaran Bulan Ini</span>
                <span class="inline-flex justify-center items-center size-8 rounded-lg bg-white/20 text-white">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 8 4-4 4 4"/><path d="M7 4v16"/><path d="M11 12h10"/><path d="M11 16h7"/><path d="M11 20h4"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-x-2">
                <h3 class="text-2xl font-extrabold text-white">-Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</h3>
            </div>
            <p class="text-xs text-white/90 mt-1">Total belanja & pengeluaran</p>
        </div>

        <!-- 4. Arus Kas Bersih (NET) (Background Putih, Teks Dinamis) -->
        <div class="bg-white rounded-xl p-4 shadow-md border-none">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Arus Kas Bersih (Net)</span>
                <span class="inline-flex justify-center items-center size-8 rounded-lg {{ $netFlow > 0 ? 'bg-emerald-100 text-emerald-700' : ($netFlow < 0 ? 'bg-rose-100 text-rose-600' : 'bg-gray-100 text-gray-700') }}">
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-x-2">
                <h3 class="text-2xl font-extrabold {{ $netFlow > 0 ? 'text-[#10B981]' : ($netFlow < 0 ? 'text-[#F43F5E]' : 'text-gray-900') }}">
                    {{ $netFlow > 0 ? '+' : ($netFlow < 0 ? '-' : '') }}Rp {{ number_format(abs($netFlow), 0, ',', '.') }}
                </h3>
            </div>
            <p class="text-xs text-gray-500 mt-1">Selisih pemasukan - pengeluaran</p>
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
                    <span class="text-xs font-semibold text-gray-800">{{ $acc->name }}</span>
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
                    <h2 class="text-base font-bold text-gray-900">Tren Keuangan Bulanan</h2>
                    <p class="text-xs text-gray-500">Perbandingan pemasukan vs pengeluaran tahun {{ date('Y') }}</p>
                </div>
            </div>
            <div id="monthly-trend-chart" class="min-h-[300px]"></div>
        </div>

        <!-- Donut Chart: Expense Breakdown -->
        <div class="bg-white rounded-xl p-5 shadow-md border-none">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Proporsi Pengeluaran</h2>
                    <p class="text-xs text-gray-500">Berdasarkan kategori bulan ini</p>
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
                    <p class="text-xs text-gray-500">Penggunaan kuota anggaran bulan {{ date('F Y') }}</p>
                </div>
                <a href="{{ route('budgets.index') }}" class="text-xs font-bold text-gray-900 hover:text-gray-700 transition">
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
                    Belum ada batas anggaran diset untuk bulan ini.
                    <br>
                    <a href="{{ route('budgets.index') }}" class="text-gray-900 font-bold underline mt-1 inline-block">Set Budget Sekarang</a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent 5 Transactions -->
        <div class="bg-white rounded-xl p-5 shadow-md border-none">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Transaksi Terakhir</h2>
                    <p class="text-xs text-gray-500">5 catatan transaksi terbaru</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-gray-900 hover:text-gray-700 transition">
                    Lihat Semua →
                </a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentTransactions as $tx)
                <div class="py-3 flex items-center justify-between">
                    <div class="flex items-center gap-x-3">
                        <div class="size-9 rounded-xl flex items-center justify-center {{ $tx->category->type === 'income' ? 'bg-gray-50 text-emerald-600 border border-gray-200' : 'bg-gray-50 text-[#F43F5E] border border-gray-200' }}">
                            @if($tx->category->type === 'income')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M20.24 3.75a.75.75 0 0 1-.75.75H8.989v13.939l2.47-2.47a.75.75 0 1 1 1.06 1.061l-3.75 3.75a.75.75 0 0 1-1.06 0l-3.751-3.75a.75.75 0 1 1 1.06-1.06l2.47 2.469V3.75a.75.75 0 0 1 .75-.75H19.49a.75.75 0 0 1 .75.75Z" clip-rule="evenodd" />
</svg>

                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
  <path fill-rule="evenodd" d="M20.24 20.249a.75.75 0 0 0-.75-.75H8.989V5.56l2.47 2.47a.75.75 0 0 0 1.06-1.061l-3.75-3.75a.75.75 0 0 0-1.06 0l-3.75 3.75a.75.75 0 1 0 1.06 1.06l2.47-2.469V20.25c0 .414.335.75.75.75h11.25a.75.75 0 0 0 .75-.75Z" clip-rule="evenodd" />
</svg>

                            @endif
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
                    Belum ada transaksi recorded.
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
