@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header pb-0">
        <div class="row">
            <div class="col-6">
                <h5>{{ $title }}</h5>
            </div>
            <div class="col-6 text-end">
                <a href="{{ route('management-payment-create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                    <i class="fas fa-plus"></i>&nbsp; Tambah
                </a>
            </div>
        </div>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table align-items-center mb-0" id="table-data" style="width: 100%">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" width="5%">No</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipe</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
