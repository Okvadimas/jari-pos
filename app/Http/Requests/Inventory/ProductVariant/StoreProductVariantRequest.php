<?php

namespace App\Http\Requests\Inventory\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class StoreProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek akses ke menu Inventori Product (code: IN-03)
        return Gate::allows('access-menu', 'IN-04');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productvariantId = $this->input('id'); 

        return [
            'id'            => 'nullable|exists:product_variants,id',
            'product'       => 'required|exists:products,id',
            'name'          => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_variants', 'name')
                    ->ignore($productvariantId)
                    ->whereNull('deleted_at'),
            ],
            'sku'           => [
                'required',
                'string',
                'max:100',
                Rule::unique('product_variants', 'sku')
                    ->ignore($productvariantId)
                    ->whereNull('deleted_at'),
            ],  
            'edit_price'        => 'string',
            'purchase_price'    => 'required_if:id,null',
            'sell_price'        => 'required_if:id,null',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'Nama varian wajib diisi.',
            'name.string'           => 'Nama varian harus berupa string.',
            'name.max'              => 'Nama varian tidak boleh lebih dari 100 karakter.',
            'name.unique'           => 'Nama varian sudah terdaftar.',
            'product.required'      => 'Produk wajib diisi.',
            'product.exists'        => 'Produk tidak ditemukan.',
            'sku.required'          => 'SKU wajib diisi.',
            'sku.string'            => 'SKU harus berupa string.',
            'sku.max'               => 'SKU tidak boleh lebih dari 100 karakter.',
            'sku.unique'            => 'SKU sudah terdaftar.',
            'purchase_price.required_if' => 'Harga beli wajib diisi.',
            'purchase_price.string'     => 'Harga beli harus berupa string.',
            'sell_price.required_if'    => 'Harga jual wajib diisi.',
            'sell_price.string'         => 'Harga jual harus berupa string.',
        ];
    }
}
