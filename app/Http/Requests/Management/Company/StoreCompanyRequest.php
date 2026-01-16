<?php

namespace App\Http\Requests\Management\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:companies,name',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            'name.required' => 'Nama perusahaan wajib diisi.',
            'name.string' => 'Nama perusahaan harus berupa string.',
            'name.max' => 'Nama perusahaan tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama perusahaan sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'logo.file' => 'Logo harus berupa file.',
            'logo.mimes' => 'Logo harus berupa gambar dengan format: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'Ukuran logo tidak boleh lebih dari 2MB.',
        ];
    }

}
