<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesRequest extends FormRequest
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
            'id' => 'nullable|integer|exists:sales_orders,id',
            'customer_name' => 'nullable|string|max:255',
            'order_date' => 'required|string',
            'payment_method_id' => 'nullable|integer|exists:payment_methods,id',
            'total_discount_manual' => 'nullable|numeric|min:0',
            'details' => 'required|array|min:1',
            'details.*.product_variant_id' => 'required|integer|exists:product_variants,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.unit_price' => 'required|numeric|min:0',
            'details.*.discount_auto_amount' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_date.required' => 'Tanggal penjualan wajib diisi',
            'details.required' => 'Minimal harus ada 1 item penjualan',
            'details.min' => 'Minimal harus ada 1 item penjualan',
            'details.*.product_variant_id.required' => 'Produk wajib dipilih',
            'details.*.product_variant_id.exists' => 'Produk tidak valid',
            'details.*.quantity.required' => 'Jumlah wajib diisi',
            'details.*.quantity.min' => 'Jumlah minimal 1',
            'details.*.unit_price.required' => 'Harga wajib diisi',
            'details.*.unit_price.min' => 'Harga tidak boleh negatif',
        ];
    }
}
