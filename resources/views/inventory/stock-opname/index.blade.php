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
                            <a href="{{ route('inventory.stock-opname.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Tambah Stock Opname</span></a>
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
                                            <label class="form-label">Tanggal Opname</label>
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
                                            <label class="form-label">Status</label>
                                            <select class="form-select" id="filter_status">
                                                <option value="all">Semua</option>
                                                <option value="draft">Draft</option>
                                                <option value="approved">Approved</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
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
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Total Opname</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" title="Jumlah stock opname"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount" id="summary-total-opname">0</span>
                                    </div>
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Total Selisih Plus</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" title="Total item dengan stok fisik lebih banyak dari sistem"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount text-success" id="summary-selisih-plus">0</span>
                                    </div>
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-0">
                                        <div class="card-title">
                                            <h6 class="subtitle">Total Selisih Minus</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" title="Total item dengan stok fisik lebih sedikit dari sistem"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount text-danger" id="summary-selisih-minus">0</span>
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
                                        <th>Nomor Opname</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th class="text-center">Total Item</th>
                                        <th class="text-center">Total Selisih</th>
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
                                <h5 class="modal-title">Detail Stock Opname</h5>
                                <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <em class="icon ni ni-cross"></em>
                                </a>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="form-label text-muted">Nomor Opname</label>
                                            <div class="form-control-wrap">
                                                <span class="fs-5 fw-bold" id="detail-number">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="form-label text-muted">Tanggal</label>
                                            <div class="form-control-wrap">
                                                <span class="fs-5" id="detail-date">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="form-label text-muted">Status</label>
                                            <div class="form-control-wrap">
                                                <span id="detail-status">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="detail-approval-info" class="row g-3 mb-3" style="display: none;">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label text-muted">Di-approve oleh</label>
                                            <div class="form-control-wrap">
                                                <span id="detail-approved-by">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label text-muted">Tanggal Approve</label>
                                            <div class="form-control-wrap">
                                                <span id="detail-approved-at">-</span>
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
                                                <th class="text-end">Stok Sistem</th>
                                                <th class="text-end">Stok Fisik</th>
                                                <th class="text-end">Selisih</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detail-items">
                                            <!-- Items will be populated via JS -->
                                        </tbody>
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
