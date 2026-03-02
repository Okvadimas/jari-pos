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
                                <button type="button" class="btn btn-primary btn-generate">
                                    <em class="icon ni ni-plus me-1"></em>
                                    Tambah Rekomendasi Baru
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block-head-content mt-3 d-block d-lg-none">
                        <div>
                            <button type="button" class="btn btn-primary w-100 d-flex justify-content-center align-items-center btn-generate">
                                <em class="icon ni ni-plus me-1"></em>
                                Tambah Rekomendasi Baru
                            </button>
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
                                            <h4 class="amount mt-1 mb-0" id="stat-fast">0</h4>
                                        </div>
                                        <div class="icon-circle bg-success-dim">
                                            <em class="icon ni ni-trend-up" style="font-size: 1.5rem; color: #1ee0ac;"></em>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #1ee0ac, #56f5cc);" id="bar-fast"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted" id="pct-fast-text">0% dari total</span>
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
                                            <h4 class="amount mt-1 mb-0" id="stat-medium">0</h4>
                                        </div>
                                        <div class="icon-circle bg-warning-dim">
                                            <em class="icon ni ni-activity-round" style="font-size: 1.5rem; color: #f4bd0e;"></em>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #f4bd0e, #ffd748);" id="bar-medium"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted" id="pct-medium-text">0% dari total</span>
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
                                            <h4 class="amount mt-1 mb-0" id="stat-slow">0</h4>
                                        </div>
                                        <div class="icon-circle" style="background: rgba(253, 126, 20, 0.1);">
                                            <em class="icon ni ni-trend-down" style="font-size: 1.5rem; color: #fd7e14;"></em>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #fd7e14, #ffad60);" id="bar-slow"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted" id="pct-slow-text">0% dari total</span>
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
                                            <h4 class="amount mt-1 mb-0" id="stat-dead">0</h4>
                                        </div>
                                        <div class="icon-circle bg-danger-dim">
                                            <em class="icon ni ni-alert-fill" style="font-size: 1.5rem; color: #e85347;"></em>
                                        </div>
                                    </div>
                                    <div class="progress progress-sm mt-2" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%; background: linear-gradient(90deg, #e85347, #ff8a82);" id="bar-dead"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted" id="pct-dead-text">0% dari total</span>
                                        <span class="badge bg-danger-dim text-danger fs-11px">
                                            <em class="icon ni ni-alert"></em> Kritis
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- Summary Cards -->


                <!-- History List -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            @if($histories->count() > 0)
                                <div class="table-responsive">
                                    <table class="table nowrap table-striped" id="table-recommendation">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="50">No</th>
                                                <th width="200">Aksi</th>
                                                <th>Tanggal Analisis</th>
                                                <th>Total Varian</th>
                                                <th>Fast</th>
                                                <th>Medium</th>
                                                <th>Slow</th>
                                                <th>Dead</th>
                                                <th>Cogs Saldo</th>
                                                <th>Nominal Rekomendasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <em class="icon ni ni-inbox fs-3 d-block mb-2"></em>
                                    <p>Belum ada riwayat analisis. Klik "Tambah Rekomendasi Baru" untuk memulai.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal Generate Analisis Baru -->
                <div class="modal fade" tabindex="-1" id="modalGenerate">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="#" id="form-generate" class="form-validate is-alter">
                                <div class="modal-header">
                                    <h5 class="modal-title">Tambah Rekomendasi</h5>
                                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <em class="icon ni ni-cross"></em>
                                    </a>
                                </div>
                                <div class="modal-body">
                                    <p class="text-soft">Sistem akan menganalisis data penjualan berdasarkan tanggal mulai yang Anda pilih hingga data hari ini dan mengklasifikasikan semua produk.</p>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="start_date">Tanggal Mulai Analisis <span class="text-danger">*</span></label>
                                        <div class="form-control-wrap">
                                            <div class="form-icon form-icon-right">
                                                <em class="icon ni ni-calendar"></em>
                                            </div>
                                            <input type="text" class="form-control date-picker" id="start_date" name="start_date" data-date-format="dd/mm/yyyy" placeholder="Pilih Tanggal Mulai" autocomplete="off" value="{{ date('d/m/Y', strtotime('-7 days')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-submit-generate">
                                        <span>Proses Analisis</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End Modal -->

            </div>
        </div>
    </div>
</div>

<script>
    window.TodayHistoryId = {{ $todayHistory ? $todayHistory->id : 'null' }};
</script>
@endsection
