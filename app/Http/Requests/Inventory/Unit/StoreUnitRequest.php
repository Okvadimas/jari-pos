<?php

namespace App\Http\Requests\Inventory\Unit;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|string|max:10|unique:units,code',
            'name' => 'required|string|max:50',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required' => 'Kode satuan wajib diisi.',
            'code.string' => 'Kode satuan harus berupa string.',
            'code.max' => 'Kode satuan tidak boleh lebih dari 10 karakter.',
            'code.unique' => 'Kode satuan sudah terdaftar.',
            'name.required' => 'Nama satuan wajib diisi.',
            'name.string' => 'Nama satuan harus berupa string.',
            'name.max' => 'Nama satuan tidak boleh lebih dari 50 karakter.',
        ];
    }

}
