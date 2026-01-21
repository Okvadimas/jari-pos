@extends('layouts.base')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Dashboard Overview</h3>
                                <div class="nk-block-des text-soft">
                                    <p>Welcome back, {{ ucwords(Auth::user()->name) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <!-- Key Metrics -->
                        <div class="row g-gs">
                            <!-- Daily Revenue -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Daily Revenue</h6>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount">Rp {{ number_format($dailyRevenue, 0, ',', '.') }}</div>
                                                    <div class="nk-ecwg6-ck">
                                                        <!-- Optional small chart or icon -->
                                                    </div>
                                                </div>
                                                <div class="info"><span class="change up text-danger">{{ \Carbon\Carbon::now()->format('d M Y') }}</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Monthly Revenue -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Monthly Revenue</h6>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
                                                </div>
                                                <div class="info"><span class="change up text-danger">This Month</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Profit -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Est. Profit (Month)</h6>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount {{ $monthlyProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                                        Rp {{ number_format($monthlyProfit, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                                <div class="info"><span class="sub-text">Rev - COGS (Avg Cost)</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Low Stock Alert -->
                            <div class="col-xxl-3 col-sm-6">
                                <div class="card h-100">
                                    <div class="nk-ecwg nk-ecwg6">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title">Low Stock Items</h6>
                                                </div>
                                            </div>
                                            <div class="data">
                                                <div class="data-group">
                                                    <div class="amount text-warning">{{ count($lowStockProducts) }}</div>
                                                </div>
                                                <div class="info"><span class="sub-text">Products need restock</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts -->
                        <div class="nk-block nk-block-lg mt-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-bordered">
                                        <div class="card-inner">
                                            <div class="card-head">
                                                <h5 class="card-title">Sales Trend (Last 7 Days)</h5>
                                            </div>
                                            <div class="nk-ck" style="height: 300px;">
                                                <canvas class="sales-overview-chart" id="salesOverviewChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Products & Low Stock Tables -->
                        <div class="row g-gs mt-4">
                            <!-- Top Products -->
                            <div class="col-md-6">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="card-title">Top Selling Products</h5>
                                        </div>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Variant</th>
                                                    <th class="text-end">Qty</th>
                                                    <th class="text-end">Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($topProducts as $item)
                                                    <tr>
                                                        <td>{{ $item->product_name }}</td>
                                                        <td><span class="badge bg-light text-dark">{{ $item->variant_name }}</span></td>
                                                        <td class="text-end">{{ $item->total_qty }}</td>
                                                        <td class="text-end">{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">No sales data yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Low Stock -->
                            <div class="col-md-6">
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-head">
                                            <h5 class="card-title text-warning">Low Stock Warnings</h5>
                                        </div>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Variant</th>
                                                    <th class="text-end">Stock</th>
                                                    <th class="text-end">Last Update</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($lowStockProducts as $item)
                                                    <tr>
                                                        <td>{{ $item->product_name }}</td>
                                                        <td><span class="badge bg-light text-dark">{{ $item->variant_name }}</span></td>
                                                        <td class="text-end fw-bold text-danger">{{ $item->closing_stock }}</td>
                                                        <td class="text-end small">{{ \Carbon\Carbon::parse($item->last_updated)->format('d M') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">Good! No low stock items.</td>
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