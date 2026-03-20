@extends('layouts.base')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">{{ $title }}</h3>
                        <p class="nk-block-des text-soft">Monitoring pendapatan affiliate</p>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <form id="filter-form">
                                <div class="row g-4 align-items-end">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Periode</label>
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
                        <div class="col-sm-6 col-lg-4 col-xxl-2">
                            <div class="card card-bordered moving-card border-primary">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Affiliate</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-affiliate">0</h4>
                                        </div>
                                        <div class="icon-circle icon-circle-sm bg-primary-dim"><em class="icon ni ni-users text-primary"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xxl-2">
                            <div class="card card-bordered moving-card border-info">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Transaksi</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-transaksi">0</h4>
                                        </div>
                                        <div class="icon-circle icon-circle-sm bg-info-dim"><em class="icon ni ni-file-text text-info"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xxl-2">
                            <div class="card card-bordered moving-card border-success">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Penjualan</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-penjualan">Rp 0</h4>
                                        </div>
                                        <div class="icon-circle icon-circle-sm bg-success-dim"><em class="icon ni ni-wallet-in text-success"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xxl-2">
                            <div class="card card-bordered moving-card border-secondary">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Komisi</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-komisi">Rp 0</h4>
                                        </div>
                                        <div class="icon-circle icon-circle-sm bg-secondary-dim"><em class="icon ni ni-coin-alt text-secondary"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xxl-2">
                            <div class="card card-bordered moving-card border-warning">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Komisi Pending</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-komisi-pending">Rp 0</h4>
                                        </div>
                                        <div class="icon-circle icon-circle-sm bg-warning-dim"><em class="icon ni ni-clock text-warning"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xxl-2">
                            <div class="card card-bordered moving-card border-success">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Komisi Dibayar</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-komisi-paid">Rp 0</h4>
                                        </div>
                                        <div class="icon-circle icon-circle-sm bg-success-dim"><em class="icon ni ni-check-circle text-success"></em></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Per-Affiliate Summary Table -->
                <div class="nk-block nk-block-lg">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <h6 class="title mb-3">Ringkasan Per Affiliate</h6>
                            <table class="table table-striped nowrap" id="table-data">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Aksi</th>
                                        <th>Affiliate</th>
                                        <th>Kode Kupon</th>
                                        <th class="text-center">Transaksi</th>
                                        <th class="text-center">Baru</th>
                                        <th class="text-center">Perpanjangan</th>
                                        <th class="text-end">Total Penjualan</th>
                                        <th class="text-end">Total Komisi</th>
                                        <th class="text-end">Pending</th>
                                        <th class="text-end">Dibayar</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Detail Affiliate -->
                <div class="modal fade" tabindex="-1" id="modal-detail">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Affiliate: <span id="detail-affiliate-name" class="text-primary"></span></h5>
                                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close"><em class="icon ni ni-cross"></em></a>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped nowrap" id="table-detail">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th>No. Komisi</th>
                                            <th>Tanggal</th>
                                            <th>No. Penjualan</th>
                                            <th>Pelanggan</th>
                                            <th>Paket</th>
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
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
