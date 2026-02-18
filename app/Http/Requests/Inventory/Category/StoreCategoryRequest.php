<?php

namespace App\Http\Requests\Inventory\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek akses ke menu Inventori Category (code: IN-02)
        return Gate::allows('access-menu', 'IN-02');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->input('id');
        $companyId = Auth::user()->company_id;

        return [
            'id'    => 'nullable|exists:categories,id',
            'code'  => [
                'required',
                'string',
                'size:3',
                'alpha',
                Rule::unique('categories', 'code')
                    ->ignore($categoryId)
                    ->where('company_id', $companyId)
                    ->whereNull('deleted_at'),
            ],
            'name'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->ignore($categoryId)
                    ->where('company_id', $companyId)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode kategori wajib diisi.',
            'code.string'   => 'Kode kategori harus berupa string.',
            'code.size'     => 'Kode kategori harus 3 karakter.',
            'code.alpha'    => 'Kode kategori hanya boleh berisi huruf.',
            'code.unique'   => 'Kode kategori sudah terdaftar.',
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string'   => 'Nama kategori harus berupa string.',
            'name.max'      => 'Nama kategori tidak boleh lebih dari 255 karakter.',
            'name.unique'   => 'Nama kategori sudah terdaftar.',
        ];
    }
}
