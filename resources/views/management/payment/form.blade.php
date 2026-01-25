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
                            <div class="nk-block-head-content d-none d-lg-block">
                                <a href="{{ route('management-payment') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('management-payment') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">{{ isset($payment) ? 'Edit' : 'Tambah' }} Metode Pembayaran</h5>
                                <p>{{ isset($payment) ? 'Edit data metode pembayaran' : 'Menambahkan metode pembayaran baru' }}</p>
                                <form id="form-data" class="gy-3 form-settings">
                                    <input type="hidden" name="id" value="{{ isset($payment) ? $payment->id : '' }}">

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Nama Pembayaran <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan nama pembayaran</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input class="form-control" type="text" id="name" name="name" value="{{ isset($payment) ? $payment->name : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="type">Tipe <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan tipe metode pembayaran</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <select class="form-control js-select2" id="type" name="type">
                                                        <option value="">Pilih Tipe Pembayaran</option>
                                                        <option value="cash" {{ isset($payment) && $payment->type == 'cash' ? 'selected' : '' }}>Cash</option>
                                                        <option value="bank_transfer" {{ isset($payment) && $payment->type == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                        <option value="e-wallet" {{ isset($payment) && $payment->type == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                                        <option value="other" {{ isset($payment) && $payment->type == 'other' ? 'selected' : '' }}>Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-lg-7 offset-lg-5">
                                            <div class="form-group mt-2">
                                                <button type="submit" class="btn btn-primary"><em class="icon ni ni-save"></em><span>Simpan</span></button>
                                            </div>
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
