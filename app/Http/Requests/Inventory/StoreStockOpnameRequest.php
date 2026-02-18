<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockOpnameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id'                             => 'nullable|integer|exists:stock_opnames,id',
            'opname_date'                    => 'required|string',
            'notes'                          => 'nullable|string|max:1000',
            'details'                        => 'required|array|min:1',
            'details.*.product_variant_id'   => 'required|integer|exists:product_variants,id',
            'details.*.system_stock'         => 'required|integer|min:0',
            'details.*.physical_stock'       => 'required|integer|min:0',
            'details.*.notes'               => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'opname_date.required'                      => 'Tanggal opname wajib diisi',
            'details.required'                          => 'Minimal harus ada 1 item produk',
            'details.min'                               => 'Minimal harus ada 1 item produk',
            'details.*.product_variant_id.required'     => 'Produk wajib dipilih',
            'details.*.product_variant_id.exists'       => 'Produk tidak valid',
            'details.*.system_stock.required'            => 'Stok sistem wajib diisi',
            'details.*.system_stock.min'                 => 'Stok sistem tidak boleh negatif',
            'details.*.physical_stock.required'          => 'Stok fisik wajib diisi',
            'details.*.physical_stock.min'               => 'Stok fisik tidak boleh negatif',
        ];
    }
}
