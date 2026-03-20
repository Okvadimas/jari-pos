@extends('layouts.base')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Halo, {{ Auth::guard('affiliator')->user()->name }}! 👋</h3>
                        <p class="nk-block-des text-soft">Pantau performa dan komisi Anda di sini.</p>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Affiliate Code Card -->
                        <div class="col-lg-4">
                            <div class="card card-bordered bg-primary-dim h-100">
                                <div class="card-inner">
                                    <h6 class="overline-title text-primary">Kode Affiliate Anda</h6>
                                    <div class="d-flex align-items-center justify-content-between mt-2">
                                        <h2 class="text-primary mb-0" id="affiliate-code">{{ Auth::guard('affiliator')->user()->code }}</h2>
                                        <button class="btn btn-icon btn-primary rounded-circle shadow-sm" onclick="copyCode()" title="Salin Kode">
                                            <em class="icon ni ni-copy"></em>
                                        </button>
                                    </div>
                                    <p class="fs-12px text-soft mt-3">Bagikan kode ini ke calon pelanggan untuk mendapatkan komisi 20% bagi Anda dan diskon 20% bagi mereka!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards Container -->
                        <div class="col-lg-8">
                            <div class="row g-gs">
                                <div class="col-sm-6 col-md-4">
                                    <div class="card card-bordered shadow-sm">
                                        <div class="card-inner text-center">
                                            <span class="fs-11px text-uppercase fw-bold text-muted">Total Transaksi</span>
                                            <h4 class="amount mt-1 mb-0" id="summary-total-transaksi">0</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="card card-bordered shadow-sm border-warning">
                                        <div class="card-inner text-center">
                                            <span class="fs-11px text-uppercase fw-bold text-muted">Komisi Pending</span>
                                            <h4 class="amount mt-1 mb-0 text-warning" id="summary-komisi-pending">Rp 0</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="card card-bordered shadow-sm border-success bg-white">
                                        <div class="card-inner text-center">
                                            <span class="fs-11px text-uppercase fw-bold text-muted">Komisi Cair</span>
                                            <h4 class="amount mt-1 mb-0 text-success" id="summary-komisi-paid">Rp 0</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter & Table -->
                <div class="nk-block mt-4">
                    <div class="card card-bordered card-preview rounded-4 overflow-hidden border-light shadow-sm">
                        <div class="card-inner border-bottom border-light">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h6 class="title">Riwayat Komisi</h6>
                                </div>
                                <div class="card-tools">
                                    <div class="form-inline gap-2">
                                        <div class="form-control-wrap">
                                            <div class="input-daterange date-picker-range input-group">
                                                <input type="text" class="form-control form-control-sm" id="start_date" value="{{ $startDate }}" data-date-format="dd/mm/yyyy" />
                                                <input type="text" class="form-control form-control-sm" id="end_date" value="{{ $endDate }}" data-date-format="dd/mm/yyyy" />
                                            </div>
                                        </div>
                                        <button type="button" id="btn-filter" class="btn btn-sm btn-icon btn-primary"><em class="icon ni ni-filter"></em></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-inner p-0">
                            <table class="table table-tranx" id="table-portal">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col">No.</th>
                                        <th class="nk-tb-col">Tanggal</th>
                                        <th class="nk-tb-col text-end">Nilai Jual</th>
                                        <th class="nk-tb-col">Komisi (%)</th>
                                        <th class="nk-tb-col text-end">Pendapatan</th>
                                        <th class="nk-tb-col">Status</th>
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

@push('scripts')
<script>
    const token = '{{ csrf_token() }}';
    
    function copyCode() {
        const code = document.getElementById('affiliate-code').innerText;
        navigator.clipboard.writeText(code).then(() => {
            NioApp.Toast('Kode Affiliate berhasil disalin!', 'success', {position: 'top-right'});
        });
    }
</script>
<script src="{{ asset('resources/js/pages/affiliator/portal/index.js') }}"></script>
@endpush
