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
                                <a href="{{ route('inventory.stock-opname.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('inventory.stock-opname.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">{{ isset($opname) ? 'Edit' : 'Tambah' }} Stock Opname</h5>
                                <p>{{ isset($opname) ? 'Edit data stock opname' : 'Menambahkan stock opname baru (disimpan sebagai draft)' }}</p>
                                <form id="form-data" class="gy-3">
                                    <input type="hidden" name="id" value="{{ isset($opname) ? $opname->id : '' }}">
                                    
                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="opname_date">Tanggal Opname <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control date-picker" id="opname_date" name="opname_date" data-date-format="dd/mm/yyyy" value="{{ isset($opname) ? \Carbon\Carbon::parse($opname->opname_date)->format('d/m/Y') : date('d/m/Y') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label class="form-label" for="notes">Catatan</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="notes" name="notes" value="{{ isset($opname) ? $opname->notes : '' }}" placeholder="Catatan opname (opsional)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="divider mt-4 mb-3"></div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Item Produk</h6>
                                                <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary">
                                                    <em class="icon ni ni-plus"></em> Tambah Item
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="table-items">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 30%;">Produk <span class="text-danger">*</span></th>
                                                            <th style="width: 15%;" class="text-end">Stok Sistem</th>
                                                            <th style="width: 15%;" class="text-end">Stok Fisik <span class="text-danger">*</span></th>
                                                            <th style="width: 15%;" class="text-end">Selisih</th>
                                                            <th style="width: 20%;">Catatan</th>
                                                            <th style="width: 5%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="item-rows">
                                                        <!-- Items will be added here dynamically -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-3">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" id="btn-save" class="btn btn-primary"><em class="icon ni ni-save"></em><span>Simpan (Draft)</span></button>
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

    <script>
        // Pass existing opname details to JavaScript for edit mode
        @php
            $details = [];
            if (isset($opname)) {
                $details = $opname->details->map(function($d) {
                    return [
                        'product_variant_id' => $d->product_variant_id,
                        'product_name' => optional($d->variant->product)->name . (optional($d->variant)->name ? ' - ' . $d->variant->name : '') . ' (' . optional($d->variant)->sku . ')',
                        'system_stock' => $d->system_stock,
                        'physical_stock' => $d->physical_stock,
                        'difference' => $d->difference,
                        'notes' => $d->notes,
                    ];
                })->toArray();
            }
        @endphp
        window.existingDetails = @json($details);
    </script>

@endsection
