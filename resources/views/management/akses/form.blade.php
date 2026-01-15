@extends('layouts.base')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Manajemen Akses</h3>
                            </div>
                            <div class="nk-block-head-content d-none d-lg-block">
                                <a href="{{ route('akses-management') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('akses-management') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">{{ isset($akses) ? 'Edit Akses' : 'Tambah Akses' }}</h5>
                                <p>{{ isset($akses) ? 'Mengubah data akses' : 'Menambahkan akses baru' }}</p>
                                <form class="gy-3 form-settings" id="form">
                                    <input type="hidden" name="id" id="id" value="{{ isset($akses) ? $akses->id : '' }}">

                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="site-email">Nama</label>
                                                <span class="form-note">Masukkan nama role/paket</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ isset($akses) ? $akses->name : '' }}" placeholder="Contoh: Basic" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <table class="table table-striped table-bordered nowrap" id="table-data">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">NO</th>
                                                        <th class="text-center">AKSI</th>
                                                        <th>KELOMPOK</th>
                                                        <th>MENU</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($menus as $menu)
                                                        <tr>
                                                            <td width="5%" class="text-center">{{ $loop->iteration }}</td>
                                                            <td width="5%">
                                                                <div class="form-check d-flex justify-content-center">
                                                                    <input class="form-check-input" style="width: 1.5em; height: 1.5em;" type="checkbox" value="{{ $menu->id }}" name="menus[]" id="menu-{{ $menu->id }}">
                                                                </div>
                                                            </td>
                                                            <td>{{ $menu->parent_name }}</td>
                                                            <td>{{ $menu->name }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
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