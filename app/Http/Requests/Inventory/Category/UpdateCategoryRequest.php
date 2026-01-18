<?php

namespace App\Http\Requests\Inventory\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
        ];
    }

    public function messages() 
    {
        return [
            'id.required' => 'ID Kategori harus ada',
            'id.exists' => 'ID Kategori tidak ditemukan',
            'name.required' => 'Nama Kategori harus diisi',
            'name.max' => 'Nama Kategori tidak boleh lebih dari 255 karakter.',
        ];
    }
}
