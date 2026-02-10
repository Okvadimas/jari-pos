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
                                <a href="{{ route('transaction.sales.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                            </div>
                        </div>
                        <div class="nk-block-head-content mt-3 d-block d-lg-none">
                            <a href="{{ route('transaction.sales.index') }}" class="btn btn-primary"><em class="icon ni ni-arrow-left"></em><span>Kembali</span></a>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="card-title mb-1">{{ isset($salesOrder) ? 'Edit' : 'Tambah' }} Penjualan</h5>
                                <p>{{ isset($salesOrder) ? 'Edit data penjualan' : 'Menambahkan transaksi penjualan baru' }}</p>
                                <form id="form-data" class="gy-3">
                                    <input type="hidden" name="id" value="{{ isset($salesOrder) ? $salesOrder->id : '' }}">
                                    
                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label" for="customer_name">Nama Pelanggan</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ isset($salesOrder) ? $salesOrder->customer_name : '' }}" placeholder="Masukkan nama pelanggan (opsional)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label" for="order_date">Tanggal Penjualan <span class="text-danger">*</span></label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control date-picker" id="order_date" name="order_date" data-date-format="dd/mm/yyyy" value="{{ isset($salesOrder) ? \Carbon\Carbon::parse($salesOrder->order_date)->format('d/m/Y') : date('d/m/Y') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label" for="payment_method_id">Metode Pembayaran</label>
                                                <div class="form-control-wrap">
                                                    <select class="form-select" id="payment_method_id" name="payment_method_id">
                                                        <option value="">Pilih Metode</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="form-label" for="total_discount_manual">Diskon Manual</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control text-end" id="total_discount_manual" name="total_discount_manual" value="{{ isset($salesOrder) ? number_format($salesOrder->total_discount_manual, 0, ',', '.') : '0' }}" placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="divider mt-4 mb-3"></div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Item Penjualan</h6>
                                                <button type="button" id="btn-add-item" class="btn btn-sm btn-outline-primary">
                                                    <em class="icon ni ni-plus"></em> Tambah Item
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="table-items">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 35%;">Produk <span class="text-danger">*</span></th>
                                                            <th style="width: 12%;" class="text-end">Jumlah <span class="text-danger">*</span></th>
                                                            <th style="width: 18%;" class="text-end">Harga Jual <span class="text-danger">*</span></th>
                                                            <th style="width: 15%;" class="text-end">Diskon</th>
                                                            <th style="width: 15%;" class="text-end">Subtotal</th>
                                                            <th style="width: 5%;"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="item-rows">
                                                        <!-- Items will be added here dynamically -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-light">
                                                            <td colspan="4" class="text-end fw-bold">Total</td>
                                                            <td class="text-end fw-bold" id="grand-total">Rp 0</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr class="table-light">
                                                            <td colspan="4" class="text-end fw-bold">Diskon Manual</td>
                                                            <td class="text-end fw-bold text-danger" id="discount-display">Rp 0</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr class="table-light">
                                                            <td colspan="4" class="text-end fw-bold">Total Bayar</td>
                                                            <td class="text-end fw-bold text-success fs-5" id="final-total">Rp 0</td>
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

    <script>
        // Pass existing sales order details to JavaScript for edit mode
        @php
            $details = [];
            if (isset($salesOrder)) {
                $details = $salesOrder->details->map(function($d) {
                    return [
                        'product_variant_id' => $d->product_variant_id,
                        'product_name' => optional($d->variant->product)->name . (optional($d->variant)->name ? ' - ' . $d->variant->name : '') . ' (' . optional($d->variant)->sku . ')',
                        'quantity' => $d->quantity,
                        'unit_price' => $d->unit_price,
                        'discount_auto_amount' => $d->discount_auto_amount,
                        'subtotal' => $d->subtotal,
                    ];
                })->toArray();
            }
        @endphp
        window.existingDetails = @json($details);
        window.existingPaymentMethodId = @json(isset($salesOrder) ? $salesOrder->payment_method_id : null);
    </script>

@endsection
