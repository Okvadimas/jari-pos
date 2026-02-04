@extends('layouts.base')

@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Dashboard</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Selamat datang, {{ ucwords(Auth::user()->name) }}!</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <span class="sub-text text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <!-- Row 1: Revenue & Profit Metrics -->
                        <div class="row g-gs">
                            <!-- Pendapatan Hari Ini -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Pendapatan Hari Ini</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <em class="icon ni ni-wallet-fill text-primary" style="font-size: 1.5rem;"></em>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="info">
                                                    <span class="badge bg-primary-dim text-primary">{{ $dailyTransactionCount }} transaksi</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pendapatan Bulan Ini -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Pendapatan Bulan Ini</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <em class="icon ni ni-growth-fill text-success" style="font-size: 1.5rem;"></em>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="info">
                                                    <span class="badge bg-success-dim text-success">{{ $monthlyTransactionCount }} transaksi</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estimasi Profit -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Estimasi Profit Bulan Ini</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <em class="icon ni ni-trend-up text-info" style="font-size: 1.5rem;"></em>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount {{ $monthlyProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                                        Rp {{ number_format($monthlyProfit, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                <div class="info">
                                                    <span class="text-muted small">Pendapatan - HPP</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pengeluaran Pembelian -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Pembelian Bulan Ini</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <em class="icon ni ni-cart-fill text-danger" style="font-size: 1.5rem;"></em>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount text-danger">Rp {{ number_format($monthlyPurchaseExpenses, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="info">
                                                    <span class="text-muted small">Total pengeluaran supplier</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Additional Metrics -->
                        <div class="row g-gs mt-1">
                            <!-- Rata-rata Order -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Rata-rata Order (AOV)</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <em class="icon ni ni-cc-alt-fill text-purple" style="font-size: 1.5rem;"></em>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="info">
                                                    <span class="text-muted small">Per transaksi bulan ini</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Promo Aktif -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Promo Aktif</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <em class="icon ni ni-offer-fill text-warning" style="font-size: 1.5rem;"></em>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount text-warning">{{ count($activePromotions) }}</div>
                                                </div>
                                                <div class="info">
                                                    <span class="text-muted small">Promo sedang berjalan</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stok Rendah -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Produk Stok Rendah</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <em class="icon ni ni-alert-fill text-danger" style="font-size: 1.5rem;"></em>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount {{ count($lowStockProducts) > 0 ? 'text-danger' : 'text-success' }}">{{ count($lowStockProducts) }}</div>
                                                </div>
                                                <div class="info">
                                                    <span class="text-muted small">Perlu restok (≤10 pcs)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Produk -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Inventori</h6>
                                                </div>
                                                <div class="card-tools">
                                                    <em class="icon ni ni-package-fill text-azure" style="font-size: 1.5rem;"></em>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount">{{ $totalProducts }}</div>
                                                </div>
                                                <div class="info">
                                                    <span class="badge bg-light text-dark me-1">{{ $totalVariants }} varian</span>
                                                    <span class="badge bg-light text-dark">{{ $totalCategories }} kategori</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Chart -->
                        <div class="row g-gs mt-3">
                            <div class="col-md-12">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-3">
                                            <div class="card-title">
                                                <h6 class="title">Tren Penjualan (7 Hari Terakhir)</h6>
                                            </div>
                                        </div>
                                        <div class="nk-ck" style="height: 280px;">
                                            <canvas class="sales-overview-chart" id="salesOverviewChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Promotions Banner -->
                        @if(count($activePromotions) > 0)
                        <div class="row g-gs mt-3">
                            <div class="col-12">
                                <div class="card card-bordered" style="background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%); border-left: 4px solid #ffc107; height: auto;">
                                    <div class="card-inner py-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <em class="icon ni ni-gift text-warning me-2" style="font-size: 1.25rem;"></em>
                                            <h6 class="mb-0 fw-bold text-dark">Promo Aktif</h6>
                                            <span class="badge bg-warning text-dark ms-2">{{ count($activePromotions) }}</span>
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @foreach($activePromotions as $promo)
                                            <div class="badge-promo d-inline-flex align-items-center bg-white border border-warning rounded-pill px-3 py-2 shadow-sm">
                                                <em class="icon ni ni-ticket text-warning me-1"></em>
                                                <span class="fw-medium text-dark">{{ $promo->name }}</span>
                                                <span class="mx-1 text-muted">•</span>
                                                <span class="text-success fw-bold">Rp {{ number_format($promo->discount_value, 0, ',', '.') }}</span>
                                                <span class="ms-2 text-muted small">(s/d {{ \Carbon\Carbon::parse($promo->end_date)->format('d M') }})</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Tables Section -->
                        <div class="row g-gs mt-3">
                            <!-- Produk Terlaris -->
                            <div class="col-md-4">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-3">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon ni ni-star-fill text-warning me-1"></em> Produk Terlaris</h6>
                                            </div>
                                            <span class="badge bg-light text-dark">Bulan Ini</span>
                                        </div>
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Produk</th>
                                                    <th class="text-end">Qty</th>
                                                    <th class="text-end">Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($topProducts as $item)
                                                    <tr>
                                                        <td>
                                                            <span class="fw-medium">{{ $item->product_name }}</span>
                                                            <br><small class="text-muted">{{ $item->variant_name }}</small>
                                                        </td>
                                                        <td class="text-end">{{ $item->total_qty }}</td>
                                                        <td class="text-end small">{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-3">
                                                            <em class="icon ni ni-package d-block mb-1" style="font-size: 2rem;"></em>
                                                            Belum ada data penjualan
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaksi Terbaru -->
                            <div class="col-md-4">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-3">
                                            <div class="card-title">
                                                <h6 class="title"><em class="icon ni ni-repeat text-primary me-1"></em> Transaksi Terbaru</h6>
                                            </div>
                                        </div>
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Customer</th>
                                                    <th class="text-end">Total</th>
                                                    <th class="text-end">Waktu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentTransactions as $trx)
                                                    <tr>
                                                        <td>
                                                            <span class="fw-medium">{{ $trx->customer_name ?: 'Pelanggan Umum' }}</span>
                                                        </td>
                                                        <td class="text-end">{{ number_format($trx->final_amount, 0, ',', '.') }}</td>
                                                        <td class="text-end small text-muted">{{ \Carbon\Carbon::parse($trx->created_at)->diffForHumans() }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted py-3">
                                                            <em class="icon ni ni-bag d-block mb-1" style="font-size: 2rem;"></em>
                                                            Belum ada transaksi
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Peringatan Stok Rendah -->
                            <div class="col-md-4">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-3">
                                            <div class="card-title">
                                                <h6 class="title text-danger"><em class="icon ni ni-alert-fill text-danger me-1"></em> Stok Rendah</h6>
                                            </div>
                                        </div>
                                        <table class="table table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Produk</th>
                                                    <th class="text-end">Stok</th>
                                                    <th class="text-end">Update</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($lowStockProducts as $item)
                                                    <tr>
                                                        <td>
                                                            <span class="fw-medium">{{ $item->product_name }}</span>
                                                            <br><small class="text-muted">{{ $item->variant_name }}</small>
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="badge bg-danger">{{ $item->closing_stock }}</span>
                                                        </td>
                                                        <td class="text-end small text-muted">{{ \Carbon\Carbon::parse($item->last_updated)->format('d M') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center text-success py-3">
                                                            <em class="icon ni ni-check-circle d-block mb-1" style="font-size: 2rem;"></em>
                                                            Semua stok aman!
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        window.dashboardData = {
            salesChart: {
                labels: @json($chartDates),
                values: @json($chartValues)
            }
        };
    </script>
    @vite(['resources/js/pages/dashboard/index.js'])
@endsection