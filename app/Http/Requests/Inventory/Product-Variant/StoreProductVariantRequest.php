<?php

namespace App\Http\Requests\Inventory\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek akses ke menu Inventori Product (code: IN-03)
        return Gate::allows('access-menu', 'IN-03');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->input('id');    

        return [
            'id'            => 'nullable|exists:products,id',
            'category_id'   => 'required|exists:categories,id',
            'company_id'    => 'required|exists:companies,id',
            'description'   => 'nullable|string',
            'name'          => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'name')
                    ->ignore($productId)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'Nama produk wajib diisi.',
            'name.string'           => 'Nama produk harus berupa string.',
            'name.max'              => 'Nama produk tidak boleh lebih dari 100 karakter.',
            'name.unique'           => 'Nama produk sudah terdaftar.',
            'category_id.required'  => 'Kategori produk wajib diisi.',
            'category_id.exists'    => 'Kategori produk tidak ditemukan.',
            'company_id.required'   => 'Perusahaan produk wajib diisi.',
            'company_id.exists'     => 'Perusahaan produk tidak ditemukan.',
            'description.string'    => 'Deskripsi produk harus berupa string.',
        ];
    }
}
