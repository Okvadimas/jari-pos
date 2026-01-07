@extends('layout.base')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Manajemen User</h3>
                            </div>
                            <div class="nk-block-head-content d-none d-lg-block">
                                <a href="{{ route('user-management') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('user-management') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">Tambah User</h5>
                                <p>Menambahkan user baru</p>
                                <form action="#" class="gy-3 form-settings">
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="site-name">Perusahaan</label>
                                                <span class="form-note">Pilih perusahaan yang akan di akses</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <select class="select2" id="company">
                                                        <option value="">-- Pilih Perusahaan --</option>
                                                        @foreach ($company as $item)
                                                            <option value="{{ $item->kode }}" {{ isset($user) && $item->kode == $user->company ? 'selected' : '' }}>{{ $item->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="site-email">Nama</label>
                                                <span class="form-note">Masukkan nama user</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="name" value="{{ isset($user) ? $user->name : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label">Paket</label>
                                                <span class="form-note">Pilih paket yang akan di akses</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <ul class="custom-control-group g-3 align-center flex-wrap">
                                                @foreach ($paket as $item)
                                                    <li>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input" name="paket" id="{{ $item->slug }}" value="{{ $item->slug }}" {{ isset($user) && $item->slug == $user->role ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="{{ $item->slug }}">{{ $item->nama }}</label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
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