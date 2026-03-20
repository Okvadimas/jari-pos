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
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('finance.app-sale.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Tambah Penjualan</span></a>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <form id="filter-form">
                                <div class="row g-4 align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal</label>
                                            <div class="form-control-wrap">
                                                <div class="input-daterange date-picker-range input-group">
                                                    <input type="text" class="form-control" id="start_date" name="start_date" data-date-format="dd/mm/yyyy" value="{{ $startDate }}" />
                                                    <div class="input-group-addon">s/d</div>
                                                    <input type="text" class="form-control" id="end_date" name="end_date" data-date-format="dd/mm/yyyy" value="{{ $endDate }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" id="btn-filter" class="btn btn-primary"><em class="icon ni ni-filter me-1"></em> Cari</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered moving-card border-primary">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Transaksi</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-transaksi">0</h4>
                                        </div>
                                        <div class="icon-circle bg-primary-dim"><em class="icon ni ni-file-text text-primary" style="font-size: 1.5rem;"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered moving-card border-success">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Pemasukan</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-pemasukan">Rp 0</h4>
                                        </div>
                                        <div class="icon-circle bg-success-dim"><em class="icon ni ni-wallet-in text-success" style="font-size: 1.5rem;"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered moving-card border-warning">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Pending</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-pending">0</h4>
                                        </div>
                                        <div class="icon-circle bg-warning-dim"><em class="icon ni ni-clock text-warning" style="font-size: 1.5rem;"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered moving-card border-info">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Dikonfirmasi</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-confirmed">0</h4>
                                        </div>
                                        <div class="icon-circle bg-info-dim"><em class="icon ni ni-check-circle text-info" style="font-size: 1.5rem;"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block nk-block-lg">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="table table-striped nowrap" id="table-data">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Aksi</th>
                                        <th>Nomor</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th>Paket</th>
                                        <th class="text-end">Harga Asli</th>
                                        <th class="text-end">Harga Final</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="modal fade" tabindex="-1" id="modal-detail">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Penjualan Aplikasi</h5>
                                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close"><em class="icon ni ni-cross"></em></a>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Pelanggan</label>
                                        <span class="fs-5 d-block" id="detail-customer">-</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Email</label>
                                        <span class="fs-5 d-block" id="detail-email">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Paket</label>
                                        <span class="fs-5 d-block" id="detail-plan">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Durasi</label>
                                        <span class="fs-5 d-block" id="detail-duration">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Status</label>
                                        <span class="fs-5 d-block" id="detail-status">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Harga Asli</label>
                                        <span class="fs-5 d-block" id="detail-original">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Diskon</label>
                                        <span class="fs-5 d-block text-danger" id="detail-discount">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Harga Final</label>
                                        <span class="fs-5 d-block fw-bold text-success" id="detail-final">-</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Kupon Affiliate</label>
                                        <span class="fs-5 d-block" id="detail-affiliate-coupon">-</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Kupon Diskon</label>
                                        <span class="fs-5 d-block" id="detail-voucher">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
