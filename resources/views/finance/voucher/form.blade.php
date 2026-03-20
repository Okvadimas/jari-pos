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
                            <a href="{{ route('finance.voucher.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card">
                        <div class="card-inner">
                            <h5 class="card-title mb-1">{{ isset($coupon) ? 'Edit' : 'Tambah' }} Kupon Diskon</h5>
                            <form id="form-data" class="gy-3">
                                <input type="hidden" name="id" value="{{ isset($coupon) ? $coupon->id : '' }}">

                                <div class="row g-3">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="code">Kode Kupon <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="code" name="code" value="{{ isset($coupon) ? $coupon->code : '' }}" placeholder="DISKON20" style="text-transform:uppercase">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="name">Nama Kupon <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ isset($coupon) ? $coupon->name : '' }}" placeholder="Diskon Tahun Baru">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="max_uses">Maks Penggunaan</label>
                                            <input type="number" class="form-control" id="max_uses" name="max_uses" value="{{ isset($coupon) ? $coupon->max_uses : '' }}" placeholder="Kosongkan = unlimited" min="1">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="type">Tipe Diskon <span class="text-danger">*</span></label>
                                            <select class="form-select" id="type" name="type">
                                                <option value="percentage" {{ (isset($coupon) && $coupon->type == 'percentage') ? 'selected' : '' }}>Persentase (%)</option>
                                                <option value="fixed" {{ (isset($coupon) && $coupon->type == 'fixed') ? 'selected' : '' }}>Nominal (Rp)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="value">Nilai Diskon <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="value" name="value" value="{{ isset($coupon) ? $coupon->value : '' }}" placeholder="0" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mt-4">
                                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ (!isset($coupon) || $coupon->is_active) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_active">Aktif</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="valid_from">Berlaku Dari</label>
                                            <input type="text" class="form-control date-picker" id="valid_from" name="valid_from" data-date-format="dd/mm/yyyy" value="{{ (isset($coupon) && $coupon->valid_from) ? \Carbon\Carbon::parse($coupon->valid_from)->format('d/m/Y') : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="valid_until">Berlaku Sampai</label>
                                            <input type="text" class="form-control date-picker" id="valid_until" name="valid_until" data-date-format="dd/mm/yyyy" value="{{ (isset($coupon) && $coupon->valid_until) ? \Carbon\Carbon::parse($coupon->valid_until)->format('d/m/Y') : '' }}">
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
