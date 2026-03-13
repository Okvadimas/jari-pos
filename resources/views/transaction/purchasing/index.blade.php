@extends('layouts.base')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ $title }}</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('transaction.purchasing.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Tambah Pembelian</span></a>
                        </div>
                    </div>
                </div><!-- .nk-block-head -->

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <form id="filter-form">
                                <div class="row g-4 align-items-end">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Transaksi</label>
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
                                        <div class="form-group">
                                            <button type="button" id="btn-filter" class="btn btn-primary">
                                                <em class="icon ni ni-filter me-1"></em>
                                                Cari
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div><!-- .card -->
                </div><!-- .nk-block -->

                <!-- Summary Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-sm-6 col-lg-6">
                            <div class="card card-bordered moving-card border-primary">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Transaksi</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-transaksi">0</h4>
                                        </div>
                                        <div class="icon-circle bg-primary-dim">
                                            <em class="icon ni ni-file-text text-primary" style="font-size: 1.5rem;"></em>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted" data-bs-toggle="tooltip" title="Jumlah transaksi pembelian">Jumlah transaksi pembelian</span>
                                    </div>
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-sm-6 col-lg-6">
                            <div class="card card-bordered moving-card border-danger">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div>
                                            <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Pembelian</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-pembelian">Rp 0</h4>
                                        </div>
                                        <div class="icon-circle bg-danger-dim">
                                            <em class="icon ni ni-wallet-out text-danger" style="font-size: 1.5rem;"></em>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fs-12px text-muted" data-bs-toggle="tooltip" title="Total nilai pembelian">Total nilai pembelian</span>
                                    </div>
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->
                    </div>
                </div><!-- .nk-block -->

                <div class="nk-block nk-block-lg">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="table table-striped nowrap" id="table-data">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th class="text-center">Aksi</th>
                                        <th>Nomor Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th class="text-end">Total Pembelian</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div><!-- .card-preview -->
                </div><!-- .nk-block -->

                <!-- Modal Detail -->
                <div class="modal fade" tabindex="-1" id="modal-detail">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detail Pembelian</h5>
                                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <em class="icon ni ni-cross"></em>
                                </a>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label text-muted">Supplier / Perusahaan</label>
                                            <div class="form-control-wrap">
                                                <span class="fs-5" id="detail-supplier">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label text-muted">Tanggal</label>
                                            <div class="form-control-wrap">
                                                <span class="fs-5" id="detail-date">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>SKU</th>
                                                <th class="text-end">Jumlah</th>
                                                <th class="text-end">Harga</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail-items">
                                            <!-- Items will be populated via JS -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-end">Grand Total</th>
                                                <th class="text-end fw-bold" id="detail-total">Rp 0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <span class="sub-text" id="detail-note"></span>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
@endsection

