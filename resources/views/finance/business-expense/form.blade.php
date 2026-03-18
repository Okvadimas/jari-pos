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
                            <a href="{{ route('finance.business-expense.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card">
                        <div class="card-inner">
                            <h5 class="card-title mb-1">{{ isset($expense) ? 'Edit' : 'Tambah' }} Pengeluaran</h5>
                            <p>{{ isset($expense) ? 'Edit data pengeluaran' : 'Menambahkan pengeluaran baru' }}</p>
                            <form id="form-data" class="gy-3">
                                <input type="hidden" name="id" value="{{ isset($expense) ? $expense->id : '' }}">

                                <div class="row g-3">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="category">Kategori <span class="text-danger">*</span></label>
                                            <select class="form-select" id="category" name="category">
                                                <option value="">Pilih Kategori</option>
                                                <option value="server" {{ (isset($expense) && $expense->category == 'server') ? 'selected' : '' }}>Server</option>
                                                <option value="production" {{ (isset($expense) && $expense->category == 'production') ? 'selected' : '' }}>Produksi</option>
                                                <option value="other" {{ (isset($expense) && $expense->category == 'other') ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="expense_date">Tanggal <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control date-picker" id="expense_date" name="expense_date" data-date-format="dd/mm/yyyy" value="{{ isset($expense) ? \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') : date('d/m/Y') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="amount">Nominal <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="amount" name="amount" value="{{ isset($expense) ? $expense->amount : '' }}" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label" for="vendor_name">Nama Vendor</label>
                                            <input type="text" class="form-control" id="vendor_name" name="vendor_name" value="{{ isset($expense) ? $expense->vendor_name : '' }}" placeholder="Nama vendor/penyedia">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label" for="reference_note">Catatan Referensi</label>
                                            <input type="text" class="form-control" id="reference_note" name="reference_note" value="{{ isset($expense) ? $expense->reference_note : '' }}" placeholder="No. Invoice, dll">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="form-label" for="description">Deskripsi <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Deskripsi pengeluaran">{{ isset($expense) ? $expense->description : '' }}</textarea>
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
