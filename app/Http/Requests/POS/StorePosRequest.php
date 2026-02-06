<?php

namespace App\Http\Requests\POS;

use Illuminate\Foundation\Http\FormRequest;

class StorePosRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name'         => 'required|string|max:255',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.variant_id'    => 'required|exists:product_variants,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.price'         => 'required|numeric|min:0', // We might verify this in service
            'voucher_id'            => 'nullable|exists:promotions,id',
            'payment_method_id'     => 'required|exists:payment_methods,id',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'        => 'Nama pelanggan wajib diisi.',
            'items.required'                => 'Keranjang belanja tidak boleh kosong.',
            'items.min'                     => 'Keranjang belanja tidak boleh kosong.',
            'items.*.quantity.min'          => 'Jumlah item minimal 1.',
            'items.*.variant_id.exists'     => 'Produk tidak valid.',
            'payment_method_id.required'    => 'Metode pembayaran wajib dipilih.',
            'payment_method_id.exists'      => 'Metode pembayaran tidak valid.',
        ];
    }
}
