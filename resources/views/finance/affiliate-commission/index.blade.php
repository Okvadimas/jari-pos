@extends('layouts.base')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">{{ $title }}</h3>
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
                                    <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Komisi</span>
                                    <h4 class="amount mt-1 mb-0" id="summary-total-komisi">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered moving-card border-info">
                                <div class="card-inner">
                                    <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Nominal</span>
                                    <h4 class="amount mt-1 mb-0" id="summary-total-nominal">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered moving-card border-warning">
                                <div class="card-inner">
                                    <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Pending</span>
                                    <h4 class="amount mt-1 mb-0" id="summary-total-pending">Rp 0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered moving-card border-success">
                                <div class="card-inner">
                                    <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Dibayar</span>
                                    <h4 class="amount mt-1 mb-0" id="summary-total-paid">Rp 0</h4>
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
                                        <th>Affiliate</th>
                                        <th>Kode Kupon</th>
                                        <th>Tipe</th>
                                        <th class="text-end">Nilai Jual</th>
                                        <th>Rate</th>
                                        <th class="text-end">Komisi</th>
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
                                <h5 class="modal-title">Detail Komisi Affiliate</h5>
                                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close"><em class="icon ni ni-cross"></em></a>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Affiliate</label>
                                        <span class="fs-5 d-block" id="detail-affiliate">-</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Kode Kupon</label>
                                        <span class="fs-5 d-block" id="detail-coupon">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Nilai Jual</label>
                                        <span class="fs-5 d-block" id="detail-sale-amount">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Rate Komisi</label>
                                        <span class="fs-5 d-block" id="detail-rate">-</span>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label text-muted">Nominal Komisi</label>
                                        <span class="fs-5 d-block fw-bold text-success" id="detail-commission">-</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Status</label>
                                        <span class="fs-5 d-block" id="detail-status">-</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Tanggal Bayar</label>
                                        <span class="fs-5 d-block" id="detail-paid-date">-</span>
                                    </div>
                                </div>
                                <hr>
                                <h6>Referensi Penjualan</h6>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">Pelanggan</label>
                                        <span class="fs-6 d-block" id="detail-sale-customer">-</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-muted">No. Penjualan</label>
                                        <span class="fs-6 d-block" id="detail-sale-number">-</span>
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
