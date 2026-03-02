<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekomendasi Stok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            /* margin-bottom: 20px; */
        }
        .info-table td {
            vertical-align: top;
            padding: 3px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
        }
        .bg-fast { background-color: #1ee0ac; }
        .bg-medium { background-color: #f4bd0e; }
        .bg-slow { background-color: #fd7e14; }
        .bg-dead { background-color: #e85347; }
        .summary-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-name">{{ $company->name }}</div>
        <div class="report-title">LAPORAN REKOMENDASI STOK</div>
        <div>
            Periode Analisis: {{ \Carbon\Carbon::parse($history->period_start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($history->period_end)->format('d M Y') }}
        </div>
        <div style="font-size: 10px; margin-top: 5px;">
            Dibuat pada: {{ \Carbon\Carbon::parse($history->analysis_date)->translatedFormat('d F Y') }}
        </div>
    </div>

    <div class="summary-box">
        <strong>Ringkasan Analisis:</strong>
        <table class="info-table" style="margin-top: 10px;">
            <tr>
                <td width="20%">Total Produk Dianalisis</td>
                <td width="30%">: <strong>{{ $history->total_variants }}</strong></td>
                <td width="20%">Total Balance (COGS)</td>
                <td width="30%">: <strong>Rp {{ number_format($history->cogs_balance, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Fast Moving</td>
                <td>: {{ $history->total_fast }}</td>
                <td>Estimasi Total Re-Stok</td>
                <td>: <strong style="color: #1ee0ac;">Rp {{ number_format($history->total_estimated_nominal, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td>Medium Moving</td>
                <td>: {{ $history->total_medium }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Slow Moving</td>
                <td>: {{ $history->total_slow }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Dead Stock</td>
                <td>: {{ $history->total_dead }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="30%" class="text-center">Produk</th>
                <th width="5%" class="text-center">Stok</th>
                <th width="5%" class="text-center">Terjual</th>
                <th width="10%" class="text-center">Harga Beli</th>
                <th width="5%" class="text-center">Restok</th>
                <th width="15%" class="text-center">Estimasi (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $idx => $detail)
                @php
                    $badgeClass = '';
                    $status = strtoupper($detail->moving_status);
                    if ($status == 'FAST') $badgeClass = 'bg-fast';
                    elseif ($status == 'MEDIUM') $badgeClass = 'bg-medium';
                    elseif ($status == 'SLOW') $badgeClass = 'bg-slow';
                    else $badgeClass = 'bg-dead';
                    
                    $productName = $detail->variant_name && $detail->variant_name !== '-' 
                        ? $detail->product_name . ' - ' . $detail->variant_name 
                        : $detail->product_name;
                        
                    $nominal = ($detail->qty_restock ?? 0) * $detail->purchase_price;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $productName }}</strong><br>
                        <div style="margin-top: 3px;">
                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                            <span style="color: #666; font-size: 9px;">{{ $detail->category_name ?? 'Tanpa Kategori' }}</span>
                        </div>
                    </td>
                    <td class="text-center">{{ number_format($detail->current_stock, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($detail->total_qty_sold, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($detail->purchase_price, 0, ',', '.') }}</td>
                    <td class="text-center"><strong>{{ $detail->qty_restock ?? 0 }}</strong></td>
                    <td class="text-center">{{ number_format($nominal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
