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
                            <a href="{{ route('finance.app-sale.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card">
                        <div class="card-inner">
                            <h5 class="card-title mb-1">{{ isset($sale) ? 'Edit' : 'Tambah' }} Penjualan Aplikasi</h5>
                            <form id="form-data" class="gy-3">
                                <input type="hidden" name="id" value="{{ isset($sale) ? $sale->id : '' }}">

                                <div class="row g-3">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="customer_name">Nama Pelanggan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ isset($sale) ? $sale->customer_name : '' }}" placeholder="Nama pelanggan">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="customer_email">Email</label>
                                            <input type="email" class="form-control" id="customer_email" name="customer_email" value="{{ isset($sale) ? $sale->customer_email : '' }}" placeholder="email@example.com">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="sale_date">Tanggal <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control date-picker" id="sale_date" name="sale_date" data-date-format="dd/mm/yyyy" value="{{ isset($sale) ? \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') : date('d/m/Y') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="plan_name">Nama Paket <span class="text-danger">*</span></label>
                                            <select class="form-select" id="plan_name" name="plan_name">
                                                <option value="">Pilih Paket</option>
                                                <option value="Starter" {{ (isset($sale) && $sale->plan_name == 'Starter') ? 'selected' : '' }}>Starter</option>
                                                <option value="Pro" {{ (isset($sale) && $sale->plan_name == 'Pro') ? 'selected' : '' }}>Pro</option>
                                                <option value="Enterprise" {{ (isset($sale) && $sale->plan_name == 'Enterprise') ? 'selected' : '' }}>Enterprise</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="duration_months">Durasi (bulan) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="duration_months" name="duration_months" value="{{ isset($sale) ? $sale->duration_months : 1 }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="original_amount">Harga <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="original_amount" name="original_amount" value="{{ isset($sale) ? $sale->original_amount : '' }}" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="voucher_code">Kupon Diskon</label>
                                            <input type="text" class="form-control" id="voucher_code" name="voucher_code" value="{{ isset($sale) ? $sale->voucher_code : '' }}" placeholder="Kode kupon diskon">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="affiliate_coupon_code">Kupon Affiliate</label>
                                            <input type="text" class="form-control" id="affiliate_coupon_code" name="affiliate_coupon_code" value="{{ isset($sale) ? $sale->affiliate_coupon_code : '' }}" placeholder="Kode kupon affiliate">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mt-4">
                                                <input type="checkbox" class="custom-control-input" id="is_renewal" name="is_renewal" value="1" {{ (isset($sale) && $sale->is_renewal) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_renewal">Perpanjangan</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-label" for="reference_note">Catatan</label>
                                            <textarea class="form-control" id="reference_note" name="reference_note" rows="2" placeholder="Catatan tambahan">{{ isset($sale) ? $sale->reference_note : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-3">
                                    <div class="col-12">
                                        <button type="submit" id="btn-save" class="btn btn-primary"><em class="icon ni ni-save"></em><span>Simpan</span></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
