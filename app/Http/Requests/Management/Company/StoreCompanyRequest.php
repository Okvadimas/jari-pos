<?php

namespace App\Http\Requests\Management\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek akses ke menu Manajemen Company (code: MJ-03)
        return Gate::allows('access-menu', 'MJ-03');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $companyId = $this->input('id');

        return [
            'id'        => 'nullable|exists:companies,id',
            'name'      => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')
                    ->ignore($companyId)
                    ->whereNull('deleted_at'),
            ],
            'email'     => [
                'required',
                'email',
                Rule::unique('companies', 'email')
                    ->ignore($companyId)
                    ->whereNull('deleted_at'),
            ],
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
            'logo'      => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Nama perusahaan wajib diisi.',
            'name.string'       => 'Nama perusahaan harus berupa string.',
            'name.max'          => 'Nama perusahaan tidak boleh lebih dari 255 karakter.',
            'name.unique'       => 'Nama perusahaan sudah terdaftar.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah terdaftar.',
            'phone.max'         => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'logo.file'         => 'Logo harus berupa file.',
            'logo.mimes'        => 'Logo harus berupa gambar dengan format: jpeg, png, jpg, gif, svg.',
            'logo.max'          => 'Ukuran logo tidak boleh lebih dari 2MB.',
        ];
    }
}
