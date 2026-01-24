@extends('layouts.base')

@section('content')
    <!-- content @s -->
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ $title }}</h3>
                            </div><!-- .nk-block-head-content -->

                            <div class="nk-block-head-content d-none d-lg-block">
                                <a href="{{ route('inventory-product-create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Tambah Produk</span></a>
                            </div>
                            
                        </div><!-- .nk-block-between -->
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('inventory-product-create') }}" class="btn btn-primary mb-1 w-100"><em class="icon ni ni-plus"></em><span>Tambah Produk</span></a>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="table table-striped nowrap" id="table-data">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Aksi</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div><!-- .card-preview -->
                    </div><!-- .nk-block -->
                </div> <!-- nk-block -->
            </div><!-- .components-preview -->
        </div>
    </div>
    <!-- content @e -->

    <!-- Modals Delete Produk -->
    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Produk</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah anda yakin ingin menghapus produk ini ?</p>
                    <small class="text-warning">Akan menghapus semua varian produk ini</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modals Delete Produk -->

@endsection
