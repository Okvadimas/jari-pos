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
                                <a href="{{ route('inventory-product-variant') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('inventory-product-variant') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">{{ isset($productVariant) ? 'Edit' : 'Tambah' }} Produk Varian</h5>
                                <p>{{ isset($productVariant) ? 'Edit data produk varian' : 'Menambahkan produk varian baru' }}</p>
                                <form id="form-data" class="gy-3 form-settings">
                                    <input type="hidden" name="id" value="{{ isset($productVariant) ? $productVariant->id : '' }}">

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="code">Produk <span class="text-danger">*</span></label>
                                                <span class="form-note">Pilih produk</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <select class="js-select2" id="product" name="product">
                                                        <option value="">Pilih Produk</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}" {{ isset($productVariant) && $productVariant->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Nama Varian <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan nama produk varian</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ isset($productVariant) ? $productVariant->name : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="sku">SKU <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan SKU produk varian</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="sku" name="sku" value="{{ isset($productVariant) ? $productVariant->sku : $sku }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if(isset($productVariant))
                                        <div class="row g-3 align-center">
                                            <div class="offset-lg-5 col-lg-7">
                                                <div class="form-group">
                                                    <div class="form-control-wrap">
                                                        <div class="custom-control custom-checkbox">    
                                                            <input type="checkbox" class="custom-control-input" id="edit_price" name="edit_price">    
                                                            <label class="custom-control-label" for="edit_price">Ceklis untuk mengedit harga</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="purchase_price">Harga Beli/Dasar <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan harga beli/dasar produk varian</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control currency-input" id="purchase_price" name="purchase_price" value="{{ isset($productVariant) ? formatCurrency($productPrices->purchase_price) : '' }}" {{ isset($productVariant) ? 'readonly' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="sell_price">Harga Jual <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan harga jual produk varian</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control currency-input" id="sell_price" name="sell_price" value="{{ isset($productVariant) ? formatCurrency($productPrices->sell_price) : '' }}" {{ isset($productVariant) ? 'readonly' : '' }}>
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
