<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchasingRequest extends FormRequest
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
            'id' => 'nullable|integer|exists:purchases,id',
            'supplier_name' => 'required|string|max:255',
            'purchase_date' => 'required|string',
            'reference_note' => 'nullable|string|max:1000',
            'details' => 'required|array|min:1',
            'details.*.product_variant_id' => 'required|integer|exists:product_variants,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.cost_price_per_item' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'supplier_name.required' => 'Nama supplier wajib diisi',
            'purchase_date.required' => 'Tanggal pembelian wajib diisi',
            'details.required' => 'Minimal harus ada 1 item pembelian',
            'details.min' => 'Minimal harus ada 1 item pembelian',
            'details.*.product_variant_id.required' => 'Produk wajib dipilih',
            'details.*.product_variant_id.exists' => 'Produk tidak valid',
            'details.*.quantity.required' => 'Jumlah wajib diisi',
            'details.*.quantity.min' => 'Jumlah minimal 1',
            'details.*.cost_price_per_item.required' => 'Harga wajib diisi',
            'details.*.cost_price_per_item.min' => 'Harga tidak boleh negatif',
        ];
    }
}
