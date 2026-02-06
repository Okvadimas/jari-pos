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
                                <a href="{{ route('transaction.purchasing.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('transaction.purchasing.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">{{ isset($purchase) ? 'Edit' : 'Tambah' }} Pembelian</h5>
                                <p>{{ isset($purchase) ? 'Edit data pembelian' : 'Menambahkan transaksi pembelian baru' }}</p>
                                <form id="form-data" class="gy-3">
                                    <input type="hidden" name="id" value="{{ isset($purchase) ? $purchase->id : '' }}">
                                    
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="supplier_name">Nama Supplier <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ isset($purchase) ? $purchase->supplier_name : '' }}" placeholder="Masukkan nama supplier">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label" for="purchase_date">Tanggal Pembelian <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control date-picker" id="purchase_date" name="purchase_date" data-date-format="dd/mm/yyyy" value="{{ isset($purchase) ? \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') : date('d/m/Y') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label" for="reference_note">Catatan Referensi</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="reference_note" name="reference_note" value="{{ isset($purchase) ? $purchase->reference_note : '' }}" placeholder="No. Invoice supplier, dll">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="divider mt-4 mb-3"></div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Item Pembelian</h6>
                                                <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary">
                                                    <em class="icon ni ni-plus"></em> Tambah Item
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="table-items">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 40%;">Produk <span class="text-danger">*</span></th>
                                                            <th style="width: 15%;" class="text-end">Jumlah <span class="text-danger">*</span></th>
                                                            <th style="width: 20%;" class="text-end">Harga Modal <span class="text-danger">*</span></th>
                                                            <th style="width: 20%;" class="text-end">Subtotal</th>
                                                            <th style="width: 5%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="item-rows">
                                                        <!-- Items will be added here dynamically -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-light">
                                                            <td colspan="3" class="text-end fw-bold">Total</td>
                                                            <td class="text-end fw-bold" id="grand-total">Rp 0</td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-3">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" id="btn-save" class="btn btn-primary"><em class="icon ni ni-save"></em><span>Simpan</span></button>
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

@section('scripts')
<script>
    // Pass existing purchase details to JavaScript for edit mode
    @php
        $details = [];
        if (isset($purchase)) {
            $details = $purchase->details->map(function($d) {
                return [
                    'product_variant_id' => $d->product_variant_id,
                    'product_name' => optional($d->variant->product)->name . (optional($d->variant)->name ? ' - ' . $d->variant->name : '') . ' (' . optional($d->variant)->sku . ')',
                    'quantity' => $d->quantity,
                    'cost_price_per_item' => $d->cost_price_per_item,
                ];
            })->toArray();
        }
    @endphp
    window.existingDetails = @json($details);
</script>
@endsection
