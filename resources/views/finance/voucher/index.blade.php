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
                            <a href="{{ route('finance.voucher.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Tambah Kupon</span></a>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-sm-4">
                            <div class="card card-bordered moving-card border-primary">
                                <div class="card-inner">
                                    <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Kupon</span>
                                    <h4 class="amount mt-1 mb-0" id="summary-total-kupon">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card card-bordered moving-card border-success">
                                <div class="card-inner">
                                    <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Kupon Aktif</span>
                                    <h4 class="amount mt-1 mb-0" id="summary-total-aktif">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card card-bordered moving-card border-info">
                                <div class="card-inner">
                                    <span class="fs-11px text-uppercase fw-bold text-muted letter-spacing">Total Digunakan</span>
                                    <h4 class="amount mt-1 mb-0" id="summary-total-digunakan">0</h4>
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
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Tipe</th>
                                        <th>Nilai</th>
                                        <th>Penggunaan</th>
                                        <th>Berlaku</th>
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
@endsection
