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
                                <h3 class="nk-block-title page-title">Manajemen Akses</h3>
                            </div><!-- .nk-block-head-content -->

                            <div class="nk-block-head-content d-none d-lg-block">
                                <a href="{{ route('akses-management-create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Tambah Akses</span></a>
                            </div>
                            
                        </div><!-- .nk-block-between -->
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('akses-management-create') }}" class="btn btn-primary mb-1"><em class="icon ni ni-plus"></em><span>Tambah Akses</span></a>
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
                                            <th>Role/Paket</th>
                                            <th>Status</th>
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
@endsection