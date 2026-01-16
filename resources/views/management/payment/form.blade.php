@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header pb-0">
        <h5>{{ isset($payment) ? 'Edit ' . $title : 'Tambah ' . $title }}</h5>
    </div>
    <div class="card-body">
        <form id="form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="form-control-label">Nama Pembayaran <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="name" name="name" value="{{ isset($payment) ? $payment->name : '' }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type" class="form-control-label">Tipe <span class="text-danger">*</span></label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="">Pilih Tipe</option>
                            <option value="cash" {{ isset($payment) && $payment->type == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ isset($payment) && $payment->type == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="e-wallet" {{ isset($payment) && $payment->type == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="other" {{ isset($payment) && $payment->type == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 text-end">
                    <a href="{{ route('management-payment') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
