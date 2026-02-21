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
                        <div class="nk-block-head-content">
                            <button type="button" id="btn-generate" class="btn btn-primary">
                                <em class="icon ni ni-reload me-1"></em>
                                Proses Laporan Hari Ini
                            </button>
                        </div>
                    </div>
                </div><!-- .nk-block-head -->

                <!-- Summary Cards -->
                <div class="nk-block" id="summary-section" style="display: none;">
                    <div class="row g-gs">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered" style="border-left: 4px solid #1ee0ac;">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">🟢 Fast Moving</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" title="Produk dengan penjualan tinggi (skor ≥ 70%)"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount text-success" id="summary-fast">0</span>
                                        <span class="change text-muted fs-14px">produk</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered" style="border-left: 4px solid #f4bd0e;">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">🟡 Medium Moving</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" title="Produk dengan penjualan menengah (skor 40-69%)"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" style="color:#f4bd0e;" id="summary-medium">0</span>
                                        <span class="change text-muted fs-14px">produk</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered" style="border-left: 4px solid #fd7e14;">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">🟠 Slow Moving</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" title="Produk dengan penjualan rendah (skor 15-39%)"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" style="color:#fd7e14;" id="summary-slow">0</span>
                                        <span class="change text-muted fs-14px">produk</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered" style="border-left: 4px solid #e85347;">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">🔴 Dead Stock</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" title="Produk hampir tidak terjual (skor < 15%)"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount text-danger" id="summary-dead">0</span>
                                        <span class="change text-muted fs-14px">produk</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- Summary Cards -->

                <!-- Filter Pills -->
                <div class="nk-block" id="filter-section" style="display: none;">
                    <div class="card card-bordered">
                        <div class="card-inner py-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-dim btn-outline-primary filter-status active" data-status="">
                                        Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-dim btn-outline-success filter-status" data-status="fast">
                                        🟢 Fast Moving
                                    </button>
                                    <button type="button" class="btn btn-sm btn-dim btn-outline-warning filter-status" data-status="medium">
                                        🟡 Medium
                                    </button>
                                    <button type="button" class="btn btn-sm btn-dim filter-status" data-status="slow" style="border-color:#fd7e14;color:#fd7e14;">
                                        🟠 Slow
                                    </button>
                                    <button type="button" class="btn btn-sm btn-dim btn-outline-danger filter-status" data-status="dead">
                                        🔴 Dead Stock
                                    </button>
                                </div>
                                <div class="text-muted fs-13px" id="analysis-info"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="nk-block nk-block-lg" id="table-section" style="display: none;">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="table table-striped nowrap" id="table-data">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Produk</th>
                                        <th>SKU</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Terjual</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-center">Avg/Hari</th>
                                        <th class="text-center">Skor</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- .card-preview -->
                </div><!-- .nk-block -->

                <!-- History List -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <h6 class="title mb-3">
                                <em class="icon ni ni-calendar me-1"></em>
                                Riwayat Analisis
                            </h6>
                            @if($histories->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="50">No</th>
                                                <th>Tanggal Analisis</th>
                                                <th>Periode</th>
                                                <th class="text-center">Total</th>
                                                <th class="text-center">🟢</th>
                                                <th class="text-center">🟡</th>
                                                <th class="text-center">🟠</th>
                                                <th class="text-center">🔴</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($histories as $i => $history)
                                                <tr>
                                                    <td class="text-center">{{ $i + 1 }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($history->analysis_date)->format('d M Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($history->period_start)->format('d M') }} - {{ \Carbon\Carbon::parse($history->period_end)->format('d M Y') }}</td>
                                                    <td class="text-center">{{ $history->total_variants }}</td>
                                                    <td class="text-center"><span class="text-success fw-bold">{{ $history->total_fast }}</span></td>
                                                    <td class="text-center"><span style="color:#f4bd0e;" class="fw-bold">{{ $history->total_medium }}</span></td>
                                                    <td class="text-center"><span style="color:#fd7e14;" class="fw-bold">{{ $history->total_slow }}</span></td>
                                                    <td class="text-center"><span class="text-danger fw-bold">{{ $history->total_dead }}</span></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-dim btn-outline-primary btn-view-history" data-id="{{ $history->id }}">
                                                            <em class="icon ni ni-eye me-1"></em> Lihat
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
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
</div>
@endsection
