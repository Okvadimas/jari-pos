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
                                <a href="{{ route('company-management') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('company-management') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">{{ isset($company) ? 'Edit' : 'Tambah' }} Perusahaan</h5>
                                <p>{{ isset($company) ? 'Edit data perusahaan' : 'Menambahkan perusahaan baru' }}</p>
                                <form id="form-data" class="gy-3 form-settings">
                                    @if(isset($company))
                                        <input type="hidden" name="id" value="{{ $company->id }}">
                                    @endif
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="nama">Nama Perusahaan</label>
                                                <span class="form-note">Masukkan nama perusahaan</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="nama" name="nama" value="{{ isset($company) ? $company->nama : '' }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="email">Email</label>
                                                <span class="form-note">Masukkan email perusahaan</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="email" class="form-control" id="email" name="email" value="{{ isset($company) ? $company->email : '' }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="telepon">Telepon</label>
                                                <span class="form-note">Masukkan nomor telepon</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="telepon" name="telepon" value="{{ isset($company) ? $company->telepon : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="alamat">Alamat</label>
                                                <span class="form-note">Masukkan alamat perusahaan</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control" id="alamat" name="alamat">{{ isset($company) ? $company->alamat : '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if(isset($company))
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="status">Status</label>
                                                <span class="form-note">Status aktif perusahaan</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                 <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" {{ $company->status ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="status">Aktif</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

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
