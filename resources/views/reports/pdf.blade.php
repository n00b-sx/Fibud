<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Fibud - {{ $periodLabel }}</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 20px;
            border-b: 2px solid #F97316;
            padding-bottom: 10px;
        }

        .header table {
            width: 100%;
        }

        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #F97316;
        }

        .report-subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }

        .meta-info {
            text-align: right;
            font-size: 10px;
            color: #4b5563;
        }

        .summary-grid {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .summary-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }

        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
        }

        .summary-value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
        }

        .income-text { color: #059669; }
        .expense-text { color: #111827; }
        .net-positive { color: #059669; }
        .net-negative { color: #e11d48; }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 8px;
            border-left: 3px solid #F97316;
            padding-left: 6px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        table.data-table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 10px;
        }

        .receipt-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px;
            margin-top: 4px;
            border-radius: 4px;
        }

        .receipt-title {
            font-size: 9px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 4px;
        }

        table.receipt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        table.receipt-table th {
            background-color: #e2e8f0;
            padding: 3px 5px;
            text-align: left;
            border: 1px solid #cbd5e1;
        }

        table.receipt-table td {
            padding: 3px 5px;
            border: 1px solid #cbd5e1;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .page-break { page-break-after: always; }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- HEADER LAPORAN -->
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="brand-title">Fibud • Laporan Keuangan</div>
                    <div class="report-subtitle">Periode Laporan: {{ $periodLabel }}</div>
                </td>
                <td class="meta-info">
                    <strong>Tanggal Cetak:</strong> {{ date('d M Y, H:i') }} WIB<br>
                    <strong>Pengguna:</strong> Single User (Mode Lokal)
                </td>
            </tr>
        </table>
    </div>

    <!-- EXECUTIVE SUMMARY -->
    <table class="summary-grid">
        <tr>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-label">Total Pemasukan</div>
                    <div class="summary-value income-text">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value expense-text">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-label">Arus Kas Bersih (Net)</div>
                    <div class="summary-value {{ $netFlow >= 0 ? 'net-positive' : 'net-negative' }}">
                        {{ $netFlow >= 0 ? '+' : '-' }}Rp {{ number_format(abs($netFlow), 0, ',', '.') }}
                    </div>
                </div>
            </td>
            <td width="25%">
                <div class="summary-card">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-value" style="color: #F97316;">{{ $transactions->count() }} Transaksi</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- REKAPITULASI PENGELUARAN PER KATEGORI -->
    <div class="section-title">Rekapitulasi Pengeluaran Per Kategori</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Kategori</th>
                <th class="text-center">Jumlah Transaksi</th>
                <th class="text-right">Total Alokasi</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenseCategoryBreakdown as $cat)
            <tr>
                <td class="font-bold">{{ $cat['name'] }}</td>
                <td class="text-center">{{ $cat['count'] }}x</td>
                <td class="text-right font-bold">Rp {{ number_format($cat['total'], 0, ',', '.') }}</td>
                <td class="text-right font-bold">{{ $cat['percentage'] }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center" style="color: #9ca3af;">Tidak ada pengeluaran recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- DETAIL TRANSAKSI LENGKAP -->
    <div class="section-title">Detail Rincian Transaksi & Struk Belanja</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="15%">Rekening</th>
                <th width="15%">Kategori</th>
                <th width="20%">Sumber / Tujuan</th>
                <th width="23%">Keterangan</th>
                <th width="15%" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
            <tr>
                <td>{{ $tx->date->format('d/m/Y') }}</td>
                <td class="font-bold">{{ $tx->account->name ?? '-' }}</td>
                <td>
                    <span style="font-weight: bold; color: {{ $tx->category->type === 'income' ? '#059669' : '#4b5563' }};">
                        [{{ $tx->category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}]
                    </span>
                    <br>{{ $tx->category->name }}
                </td>
                <td>
                    @if($tx->source_destination)
                        <strong>{{ $tx->category->type === 'income' ? 'Dari: ' : 'Ke: ' }}</strong>{{ $tx->source_destination }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    {{ $tx->description ?? '-' }}

                    <!-- RINCIAN STRUK BELANJA JIKA ADA -->
                    @if($tx->items->count() > 0)
                    <div class="receipt-box">
                        <div class="receipt-title">Rincian Struk Belanja ({{ $tx->items->count() }} item):</div>
                        <table class="receipt-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-right">Diskon</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tx->items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-right" style="color: #e11d48;">{{ $item->discount > 0 ? '-Rp ' . number_format($item->discount, 0, ',', '.') : '-' }}</td>
                                    <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </td>
                <td class="text-right font-bold {{ $tx->category->type === 'income' ? 'income-text' : 'expense-text' }}">
                    {{ $tx->category->type === 'income' ? '+' : '-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="color: #9ca3af;">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
