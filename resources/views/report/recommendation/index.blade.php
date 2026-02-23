@extends('layouts.base')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ $title }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Analisis pergerakan stok berdasarkan data penjualan (Hybrid: Qty + Revenue)</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content d-none d-lg-block">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="btn-generate" class="btn btn-primary">
                                    <em class="icon ni ni-reload me-1"></em>
                                    Proses Laporan Hari Ini
                                </button>
                                <div class="period-info">
                                    <em class="icon ni ni-calendar me-1"></em>
                                    <span class="fw-medium">Periode Analisis:</span>
                                    <span id="period-range">1 Jan 2026 01:00 - 30 Jan 2026 23:00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block-head-content mt-3 d-block d-lg-none">
                        <div>
                            <button type="button" id="btn-generate" class="btn btn-primary w-100 d-flex justify-content-center align-items-center">
                                <em class="icon ni ni-reload me-1"></em>
                                Proses Laporan Hari Ini
                            </button>
                            <div class="period-info mt-2 flex-column w-100" style="gap: 0;">
                                <div>
                                    <em class="icon ni ni-calendar me-1"></em>
                                    <span class="fw-medium">Periode Analisis:</span>
                                </div>
                                <span id="period-range" class="d-block mt-1 text-muted fs-13px">1 Jan 2026 01:00 - 30 Jan 2026 23:00</span>
                            </div>
                        </div>
                    </div>

                </div><!-- .nk-block-head -->

                <!-- Summary Cards -->
                <div class="nk-block" id="summary-section">
                    <!-- Row 1: Moving Status Stat Cards -->
                    <div class="row g-gs">
                        <!-- Fast Moving -->
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card card-bordered moving-card">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Fast Moving</span>
                                            <h4 class="amount mt-1 mb-0" id="stat-fast">45</h4>
                                        </div>
                                        <div class="icon-circle bg-success-dim">
                                            <em class="icon ni ni-trend-up" style="font-size: 1.5rem; color: #1ee0ac;"></em>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: 45%; background: linear-gradient(90deg, #1ee0ac, #56f5cc);" id="bar-fast"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted">45% dari total</span>
                                        <span class="badge bg-success-dim text-success fs-11px">
                                            <em class="icon ni ni-arrow-up"></em> Baik
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Medium Moving -->
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card card-bordered moving-card">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Medium Moving</span>
                                            <h4 class="amount mt-1 mb-0" id="stat-medium">28</h4>
                                        </div>
                                        <div class="icon-circle bg-warning-dim">
                                            <em class="icon ni ni-activity-round" style="font-size: 1.5rem; color: #f4bd0e;"></em>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: 28%; background: linear-gradient(90deg, #f4bd0e, #ffd748);" id="bar-medium"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted">28% dari total</span>
                                        <span class="badge bg-warning-dim fs-11px" style="color: #c59a00;">
                                            <em class="icon ni ni-minus"></em> Sedang
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slow Moving -->
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card card-bordered moving-card">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Slow Moving</span>
                                            <h4 class="amount mt-1 mb-0" id="stat-slow">15</h4>
                                        </div>
                                        <div class="icon-circle" style="background: rgba(253, 126, 20, 0.1);">
                                            <em class="icon ni ni-trend-down" style="font-size: 1.5rem; color: #fd7e14;"></em>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: 15%; background: linear-gradient(90deg, #fd7e14, #ffad60);" id="bar-slow"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted">15% dari total</span>
                                        <span class="badge fs-11px" style="background: rgba(253, 126, 20, 0.1); color: #e06a00;">
                                            <em class="icon ni ni-arrow-down"></em> Perlu Perhatian
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dead Stock -->
                        <div class="col-xxl-3 col-sm-6">
                            <div class="card card-bordered moving-card">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Dead Stock</span>
                                            <h4 class="amount mt-1 mb-0" id="stat-dead">12</h4>
                                        </div>
                                        <div class="icon-circle bg-danger-dim">
                                            <em class="icon ni ni-alert-fill" style="font-size: 1.5rem; color: #e85347;"></em>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: 12%; background: linear-gradient(90deg, #e85347, #ff8a82);" id="bar-dead"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted">12% dari total</span>
                                        <span class="badge bg-danger-dim text-danger fs-11px">
                                            <em class="icon ni ni-alert"></em> Kritis
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Visual Summary (No Charts) -->
                    <div class="row g-gs mt-1">
                        <!-- Distribusi Visual -->
                        <div class="col-md-8">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group pb-3 g-2">
                                        <div class="card-title card-title-sm">
                                            <h6 class="title">Distribusi Moving Status</h6>
                                            <p class="text-soft">Proporsi pergerakan stok berdasarkan analisis terakhir</p>
                                        </div>
                                        <div class="card-tools">
                                            <span class="badge bg-outline-light text-dark">
                                                <em class="icon ni ni-package me-1"></em> 100 Produk
                                            </span>
                                        </div>
                                    </div>

                                    <!-- CSS Stacked Horizontal Bar -->
                                    <div class="stacked-bar-container mb-4">
                                        <div class="stacked-bar" style="height: 32px; border-radius: 8px; overflow: hidden; display: flex;">
                                            <div class="stacked-segment" style="width: 45%; background: linear-gradient(135deg, #1ee0ac, #0abf8e);" data-bs-toggle="tooltip" title="Fast Moving: 45 produk (45%)"></div>
                                            <div class="stacked-segment" style="width: 28%; background: linear-gradient(135deg, #f4bd0e, #e5a800);" data-bs-toggle="tooltip" title="Medium Moving: 28 produk (28%)"></div>
                                            <div class="stacked-segment" style="width: 15%; background: linear-gradient(135deg, #fd7e14, #e66a00);" data-bs-toggle="tooltip" title="Slow Moving: 15 produk (15%)"></div>
                                            <div class="stacked-segment" style="width: 12%; background: linear-gradient(135deg, #e85347, #d43d31);" data-bs-toggle="tooltip" title="Dead Stock: 12 produk (12%)"></div>
                                        </div>
                                    </div>

                                    <!-- Legend Grid -->
                                    <div class="row g-3">
                                        <div class="col-sm-6 col-md-3">
                                            <div class="dist-legend-item d-flex flex-column justify-content-between h-100">
                                                <div>
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="dist-dot" style="background: #1ee0ac;"></span>
                                                        <span class="fw-bold fs-13px">Fast Moving</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between mt-1 mb-2">
                                                        <div class="d-flex align-items-baseline gap-1">
                                                            <span class="fs-4 fw-bold text-dark">45</span>
                                                            <span class="fs-12px text-muted">produk</span>
                                                        </div>
                                                        <span class="badge bg-success-dim text-success fs-11px">45%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="dist-legend-item d-flex flex-column justify-content-between h-100">
                                                <div>
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="dist-dot" style="background: #f4bd0e;"></span>
                                                        <span class="fw-bold fs-13px">Medium Moving</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between mt-1 mb-2">
                                                        <div class="d-flex align-items-baseline gap-1">
                                                            <span class="fs-4 fw-bold text-dark">28</span>
                                                            <span class="fs-12px text-muted">produk</span>
                                                        </div>
                                                        <span class="badge bg-warning-dim fs-11px" style="color: #c59a00;">28%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="dist-legend-item d-flex flex-column justify-content-between h-100">
                                                <div>
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="dist-dot" style="background: #fd7e14;"></span>
                                                        <span class="fw-bold fs-13px">Slow Moving</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between mt-1 mb-2">
                                                        <div class="d-flex align-items-baseline gap-1">
                                                            <span class="fs-4 fw-bold text-dark">15</span>
                                                            <span class="fs-12px text-muted">produk</span>
                                                        </div>
                                                        <span class="badge fs-11px" style="background: rgba(253,126,20,.1); color: #e06a00;">15%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <div class="dist-legend-item d-flex flex-column justify-content-between h-100">
                                                <div>
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="dist-dot" style="background: #e85347;"></span>
                                                        <span class="fw-bold fs-13px">Dead Stock</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between mt-1 mb-2">
                                                        <div class="d-flex align-items-baseline gap-1">
                                                            <span class="fs-4 fw-bold text-dark">12</span>
                                                            <span class="fs-12px text-muted">produk</span>
                                                        </div>
                                                        <span class="badge bg-danger-dim text-danger fs-11px">12%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                     </div>

                                    <!-- Grand Total Estimation -->
                                    <div class="card bg-primary-dim mt-4 border-primary border-opacity-25" style="border: 1px solid rgba(82, 100, 132, 0.2);">
                                        <div class="card-inner p-3 d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-sm-0">
                                            <div class="d-flex align-items-center w-100 w-sm-auto" style="gap: 1rem;">
                                                <div class="icon-circle bg-primary text-white flex-shrink-0" style="width: 48px; height: 48px;">
                                                    <em class="icon ni ni-coins" style="font-size: 1.5rem;"></em>
                                                </div>
                                                <div>
                                                    <h6 class="title mb-1">Grand Total Estimasi Re-Stok</h6>
                                                    <span class="text-soft fs-13px d-block d-sm-inline">Kalkulasi nominal (COGS x Qty) dari rekomendasi stok yang dimasukkan pada tabel</span>
                                                </div>
                                            </div>
                                            <div class="text-start text-sm-end mt-2 mt-sm-0 w-100 w-sm-auto border-top border-light border-sm-0 pt-2 pt-sm-0">
                                                <span class="d-inline-block d-sm-none fs-12px text-soft mb-1">Total:</span>
                                                <h3 class="text-primary mb-0 fw-bold" id="grand-total-estimation">Rp 0</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Warning Information (Jika melebihi COGS) -->
                                    <div class="alert alert-danger alert-icon mt-3 mb-0" id="estimasi-warning" style="display: none; border-radius: 8px; box-shadow: 0 4px 15px rgba(232, 83, 71, 0.15);">
                                        <em class="icon ni ni-alert-circle"></em> 
                                        <strong>Perhatian:</strong> Kalkulasi estimasi re-stok saat ini telah melebihi nilai total COGS periode analisis.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Balance -->
                        <div class="col-md-4">
                            <div class="card card-bordered h-100">
                                <div class="card-inner h-100 d-flex flex-column">
                                    <div class="card-title-group pb-3">
                                        <div class="card-title card-title-sm">
                                            <h6 class="title">Informasi Balance</h6>
                                            <p class="text-soft">Data keuangan periode analisis</p>
                                        </div>
                                    </div>

                                    <!-- Total Balance Highlight -->
                                    <div class="balance-highlight mb-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <em class="icon ni ni-coins" style="font-size: 1.25rem; color: #798bff;"></em>
                                            <span class="fs-12px text-uppercase fw-bold text-muted letter-spacing">Total Balance</span>
                                        </div>
                                        <h3 class="fw-bold mb-0" style="color: #364a63;">Rp 7.000.000</h3>
                                    </div>

                                    <hr class="my-2" style="border-color: #e5e9f2;">

                                    <!-- Gross Profit -->
                                    <div class="balance-item mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="balance-icon" style="background: rgba(186,174,255,.15);">
                                                    <em class="icon ni ni-growth" style="color: #9d8cff;"></em>
                                                </div>
                                                <span class="fw-medium text-dark">Gross Profit</span>
                                            </div>
                                            <span class="badge bg-success-dim text-success fs-11px">64.3%</span>
                                        </div>
                                        <h5 class="fw-bold mb-1 ps-4 ms-2">Rp 4.500.000</h5>
                                        <div class="progress mt-1" style="height: 5px; border-radius: 4px;">
                                            <div class="progress-bar" style="width: 64.3%; background: linear-gradient(90deg, #baaeff, #9d8cff); border-radius: 4px;"></div>
                                        </div>
                                    </div>

                                    <!-- COGS -->
                                    <div class="balance-item mb-1">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="balance-icon" style="background: rgba(125,225,248,.15);">
                                                    <em class="icon ni ni-cart" style="color: #4ecfe5;"></em>
                                                </div>
                                                <span class="fw-medium text-dark">COGS</span>
                                            </div>
                                            <span class="badge bg-warning-dim fs-11px" style="color: #c59a00;">35.7%</span>
                                        </div>
                                        <h5 class="fw-bold mb-1 ps-4 ms-2">Rp 2.500.000</h5>
                                        <div class="progress mt-1" style="height: 5px; border-radius: 4px;">
                                            <div class="progress-bar" style="width: 35.7%; background: linear-gradient(90deg, #7de1f8, #4ecfe5); border-radius: 4px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- Summary Cards -->


                <!-- History List -->
                <div class="nk-block mt-4">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            @if($histories->count() > 0)
                                <div class="table-responsive">
                                    <table class="table nowrap table-striped" id="table-recommendation">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="50">No</th>
                                                <th>Produk</th>
                                                <th class="text-center">Stok</th>
                                                <th id="th-performance">Performa (<span id="th-period-days">30</span> Hari)</th>
                                                <th class="text-end">Harga Beli</th>
                                                <th class="text-center">Rekomendasi Re-Stok (AI)</th>
                                                <th class="text-end">Estimasi Nominal</th>
                                                <th class="text-center">Keterangan (AI)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <em class="icon ni ni-inbox fs-3 d-block mb-2"></em>
                                    <p>Belum ada riwayat analisis. Klik "Proses Laporan Hari Ini" untuk memulai.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
    </div>
</div>
<script>
    window.TodayHistoryId = {{ $todayHistory ? $todayHistory->id : 'null' }};
</script>
@endsection
