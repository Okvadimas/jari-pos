<?php

namespace App\Http\Requests\Management\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ini nanti pakai gate
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Buat Custom respon message
        return [
            'company' => 'required',
            'name' => 'required',
            'paket' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'company.required' => 'Perusahaan harus diisi',
            'name.required' => 'Nama harus diisi',
            'paket.required' => 'Paket harus diisi',
        ];
    }
}