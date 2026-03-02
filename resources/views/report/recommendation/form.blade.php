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
                                <a href="{{ route('report.stock-recommendation') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                                <div class="period-info">
                                    <em class="icon ni ni-calendar me-1"></em>
                                    <span class="fw-medium">Periode Analisis:</span>
                                    <span class="period-range"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block-head-content mt-3 d-block d-lg-none">
                        <div>
                            <div class="period-info mt-2 flex-column w-100" style="gap: 0;">
                                <div>
                                    <em class="icon ni ni-calendar me-1"></em>
                                    <span class="fw-medium">Periode Analisis:</span>
                                </div>
                                <span class="d-block mt-1 text-muted fs-13px period-range">1 Jan 2026 01:00 - 30 Jan 2026 23:00</span>
                            </div>
                        </div>
                    </div>

                </div><!-- .nk-block-head -->

                <!-- Summary Cards -->
                <div class="nk-block" id="summary-section">

                    <!-- Visual Summary -->
                    <div class="row g-gs">
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
                                                <em class="icon ni ni-package"></em><span id="total-products" class="me-1"></span> Produk
                                            </span>
                                        </div>
                                    </div>

                                    <!-- CSS Stacked Horizontal Bar -->
                                    <div class="stacked-bar-container mb-4">
                                        <div class="stacked-bar" style="height: 32px; border-radius: 8px; overflow: hidden; display: flex;">
                                            <div class="stacked-segment" id="dist-bar-fast" style="width: 0%; background: linear-gradient(135deg, #1ee0ac, #0abf8e);" data-bs-toggle="tooltip" title="Fast Moving"></div>
                                            <div class="stacked-segment" id="dist-bar-medium" style="width: 0%; background: linear-gradient(135deg, #f4bd0e, #e5a800);" data-bs-toggle="tooltip" title="Medium Moving"></div>
                                            <div class="stacked-segment" id="dist-bar-slow" style="width: 0%; background: linear-gradient(135deg, #fd7e14, #e66a00);" data-bs-toggle="tooltip" title="Slow Moving"></div>
                                            <div class="stacked-segment" id="dist-bar-dead" style="width: 0%; background: linear-gradient(135deg, #e85347, #d43d31);" data-bs-toggle="tooltip" title="Dead Stock"></div>
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
                                                            <span class="fs-4 fw-bold text-dark" id="dist-stat-fast">0</span>
                                                            <span class="fs-12px text-muted">produk</span>
                                                        </div>
                                                        <span class="badge bg-success-dim text-success fs-11px" id="dist-pct-fast">0%</span>
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
                                                            <span class="fs-4 fw-bold text-dark" id="dist-stat-medium">0</span>
                                                            <span class="fs-12px text-muted">produk</span>
                                                        </div>
                                                        <span class="badge bg-warning-dim fs-11px" style="color: #c59a00;" id="dist-pct-medium">0%</span>
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
                                                            <span class="fs-4 fw-bold text-dark" id="dist-stat-slow">0</span>
                                                            <span class="fs-12px text-muted">produk</span>
                                                        </div>
                                                        <span class="badge fs-11px" style="background: rgba(253,126,20,.1); color: #e06a00;" id="dist-pct-slow">0%</span>
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
                                                            <span class="fs-4 fw-bold text-dark" id="dist-stat-dead">0</span>
                                                            <span class="fs-12px text-muted">produk</span>
                                                        </div>
                                                        <span class="badge bg-danger-dim text-danger fs-11px" id="dist-pct-dead">0%</span>
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
                                        <h3 class="fw-bold mb-0" style="color: #364a63;" id="info-total-balance">Rp 0</h3>
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
                                            <span class="badge bg-success-dim text-success fs-11px" id="info-gross-pct">0%</span>
                                        </div>
                                        <h5 class="fw-bold mb-1 ps-4 ms-2" id="info-gross-val">Rp 0</h5>
                                        <div class="progress mt-1" style="height: 5px; border-radius: 4px;">
                                            <div class="progress-bar" style="width: 0%; background: linear-gradient(90deg, #baaeff, #9d8cff); border-radius: 4px;" id="info-gross-bar"></div>
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
                                            <span class="badge bg-warning-dim fs-11px" style="color: #c59a00;" id="info-cogs-pct">0%</span>
                                        </div>
                                        <h5 class="fw-bold mb-1 ps-4 ms-2" id="info-cogs-val">Rp 0</h5>
                                        <div class="progress mt-1" style="height: 5px; border-radius: 4px;">
                                            <div class="progress-bar" style="width: 0%; background: linear-gradient(90deg, #7de1f8, #4ecfe5); border-radius: 4px;" id="info-cogs-bar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- Summary Cards -->

                @if($isEdit)
                <!-- AI Recommendation Action -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner d-flex align-items-center justify-content-between flex-wrap">
                            <div>
                                <h5 class="title mb-1"><em class="icon ni ni-spark text-primary me-1"></em> Asisten AI Rekomendasi Stok</h5>
                                <p class="text-soft fs-13px mb-0">Hasilkan rekomendasi kuantitas belanja berdasarkan performa penjualan, sisa stok, dan riwayat yang ada. AI akan menghitung kuantitas optimal untuk mempercepat perputaran inventaris Anda.</p>
                            </div>
                            <div class="text-sm-end">
                                <button type="button" class="btn" id="btn-generate-ai">
                                    <em class="icon ni ni-cpu"></em>
                                    <span>Hasilkan Rekomendasi AI</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- History List -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            @if($histories->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table-recommendation">
                                        <thead>
                                            <tr>
                                                <th class="nk-tb-col text-center" style="width: 50px;">No</th>
                                                <th class="nk-tb-col">Produk</th>
                                                <th class="nk-tb-col" style="width: 18%;">Info Stok & Penjualan</th>
                                                <th class="nk-tb-col text-center" style="width: 10%;">Performa</th>
                                                <th class="nk-tb-col text-end" style="width: 12%;">Harga Beli</th>
                                                <th class="nk-tb-col text-center" style="width: 15%;">Rekomendasi Re-Stok (User)</th>
                                                <th class="nk-tb-col text-end" style="width: 15%;">Estimasi Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($isEdit)
                                    <div class="mt-4 text-end">
                                        <button type="button" class="btn btn-primary" id="btn-save-recommendation">
                                            <em class="icon ni ni-save"></em>
                                            <span>Simpan Rekomendasi</span>
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="text-center text-muted py-4">
                                    <em class="icon ni ni-inbox fs-3 d-block mb-2"></em>
                                    <p>Riwayat analisis tidak ditemukan.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form action content removed -->

            </div>
        </div>
    </div>
</div>
<script>
    window.TodayHistoryId = {{ $todayHistory ? $todayHistory->id : 'null' }};
    window.HistoryId = {{ $historyId ?? 'null' }};
    window.IsEdit = {{ isset($isEdit) && $isEdit ? 'true' : 'false' }};
</script>
@endsection
