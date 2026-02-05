@extends('layouts.base')

@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <span class="sub-text text-muted mb-3 d-block d-md-none">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                                <h3 class="nk-block-title page-title mb-0">Dashboard</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Selamat datang, {{ ucwords(Auth::user()->name) }}!</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <span class="sub-text text-muted mb-3 d-none d-md-block">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
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

                        <!-- Active Promotions Section -->
                        @if(count($activePromotions) > 0)
                        <div class="row g-gs mt-4">
                            <div class="col-12">
                                <div class="card card-bordered border-0" style="background: linear-gradient(108deg, #FFF9E6 0%, #FFF0C2 100%); box-shadow: 0 4px 15px rgba(255, 193, 7, 0.15);">
                                    <div class="card-inner py-4">
                                        <div class="d-flex align-items-center mb-4 px-1">
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                                <em class="icon ni ni-gift text-warning" style="font-size: 1.75rem;"></em>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 fw-bold text-dark">Promo Aktif</h5>
                                                <p class="text-muted small mb-0">Nikmati penawaran spesial yang tersedia saat ini</p>
                                            </div>
                                            <span class="badge bg-warning text-dark pill ms-3 px-3 py-2 fs-13px fw-bold">{{ count($activePromotions) }} Voucher</span>
                                        </div>
                                        
                                        <!-- Horizontal Scroll Container -->
                                        <div class="d-flex flex-nowrap overflow-auto pb-4 px-2 no-scrollbar" style="scroll-behavior: smooth;">
                                            @foreach($activePromotions as $promo)
                                            <!-- Item -->
                                            <div class="flex-shrink-0 bg-white shadow-sm position-relative overflow-hidden promo-ticket me-3" style="width: 280px; min-width: 280px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.05);">
                                                <!-- Decorative Circles -->
                                                <div class="position-absolute bg-light rounded-circle" style="width: 20px; height: 20px; top: 50%; left: -12px; transform: translateY(-50%); box-shadow: inset -2px 0 3px rgba(0,0,0,0.05);"></div>
                                                <div class="position-absolute bg-light rounded-circle" style="width: 20px; height: 20px; top: 50%; right: -12px; transform: translateY(-50%); box-shadow: inset 2px 0 3px rgba(0,0,0,0.05);"></div>
                                                
                                                <div class="d-flex flex-column h-100">
                                                    <!-- Ticket Body -->
                                                    <div class="p-3 pb-2 flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <span class="badge bg-warning-dim text-warning fw-bold text-uppercase" style="letter-spacing: 0.5px;">Promo</span>
                                                            <em class="icon ni ni-ticket-fill text-light" style="font-size: 1.5rem; color: #e5e9f2 !important;"></em>
                                                        </div>
                                                        <h3 class="text-success fw-bolder mb-1">Rp {{ number_format($promo->discount_value, 0, ',', '.') }}</h3>
                                                        <h6 class="fw-bold text-dark mb-0 text-truncate" title="{{ $promo->name }}">{{ $promo->name }}</h6>
                                                    </div>
                                                    
                                                    <!-- Dashed Divider -->
                                                    <div class="w-100 px-3">
                                                        <div class="border-top border-dashed" style="border-top: 2px dashed #e5e9f2;"></div>
                                                    </div>
                                                    
                                                    <!-- Ticket Footer -->
                                                    <div class="p-3 pt-2 bg-light bg-opacity-25 mt-auto">
                                                        <div class="d-flex align-items-center justify-content-center text-muted small">
                                                            <em class="icon ni ni-calendar-alt me-1"></em>
                                                            <span>Berlaku s/d <strong class="text-dark">{{ \Carbon\Carbon::parse($promo->end_date)->format('d M Y') }}</strong></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <style>
                            .no-scrollbar::-webkit-scrollbar {
                                height: 5px;
                            }
                            .no-scrollbar::-webkit-scrollbar-track {
                                background: transparent; 
                            }
                            .no-scrollbar::-webkit-scrollbar-thumb {
                                background: rgba(255, 193, 7, 0.3); 
                                border-radius: 10px;
                            }
                            .no-scrollbar::-webkit-scrollbar-thumb:hover {
                                background: rgba(255, 193, 7, 0.6); 
                            }
                            .promo-ticket {
                                transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                            }
                            .promo-ticket:hover {
                                transform: translateY(-5px);
                                box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
                            }
                        </style>

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