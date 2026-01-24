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
                                <a href="{{ route('inventory-unit') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('inventory-unit') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">{{ isset($product) ? 'Edit' : 'Tambah' }} Produk</h5>
                                <p>{{ isset($product) ? 'Edit data produk' : 'Menambahkan produk baru' }}</p>
                                <form id="form-data" class="gy-3 form-settings">
                                    @if(isset($product))
                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                    @endif

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="code">Kategori <span class="text-danger">*</span></label>
                                                <span class="form-note">Pilih kategori produk</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <select class="js-select2" id="category_id" name="category_id" required>
                                                        <option value="">Pilih Kategori</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}" {{ isset($product) && $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (session('role') == 'super-admin')
                                        <div class="row g-3 align-center">
                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label class="form-label" for="name">Perusahaan <span class="text-danger">*</span></label>
                                                    <span class="form-note">Pilih perusahaan anda</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-7">
                                                <div class="form-group">
                                                    <div class="form-control-wrap">
                                                        <select class="js-select2" id="company_id" name="company_id" required>
                                                            <option value="">Pilih Perusahaan</option>
                                                            @foreach ($companies as $company)
                                                                <option value="{{ $company->id }}" {{ isset($product) && $product->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Input Readonly Perusahaan -->
                                        <div class="row g-3 align-center">
                                            <div class="col-lg-5">
                                                <div class="form-group">
                                                    <label class="form-label" for="company_id">Perusahaan <span class="text-danger">*</span></label>
                                                    <span class="form-note">Perusahaan anda</span>
                                                </div>
                                            </div>
                                            <div class="col-lg-7">
                                                <div class="form-group">
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="company_id" name="company_id" value="{{ isset($product) ? $product->company->name : '' }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Nama <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan nama produk</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ isset($product) ? $product->name : '' }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Deskripsi</label>
                                                <span class="form-note">Masukkan deskripsi produk</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control" id="description" name="description" rows="3">{{ isset($product) ? $product->description : '' }}</textarea>
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
