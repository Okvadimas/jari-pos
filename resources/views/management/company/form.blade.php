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
                                    <input type="hidden" name="id" value="{{ isset($company) ? $company->id : '' }}">
                                    
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Nama Perusahaan <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan nama perusahaan</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="name" name="name" value="{{ isset($company) ? $company->name : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                                <span class="form-note">Masukkan email perusahaan</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="email" name="email" value="{{ isset($company) ? $company->email : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="phone">Telepon</label>
                                                <span class="form-note">Masukkan nomor telepon</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ isset($company) ? $company->phone : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="address">Alamat</label>
                                                <span class="form-note">Masukkan alamat perusahaan</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control" id="address" name="address">{{ isset($company) ? $company->address : '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-3 align-center">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <label class="form-label" for="logo">Logo</label>
                                                <span class="form-note">Upload logo perusahaan</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <input type="file" class="form-control" id="logo" name="logo">
                                                </div>
                                                @if(isset($company) && $company->logo)
                                                    <div class="mt-2 text-soft">
                                                        <p class="mb-1">Logo saat ini:</p>
                                                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                                                    </div>
                                                @endif
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
