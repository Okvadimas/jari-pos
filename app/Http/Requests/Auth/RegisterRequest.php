<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User fields
            'name'              => 'required|string|max:255',
            'username'          => 'required|string|max:255|unique:users,username',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:4',
            // Company fields
            'company_name'      => 'required|string|max:255',
            'business_category' => 'required|in:retail,restoran',
            'company_email'     => 'required|email|unique:companies,email',
            'company_phone'     => 'nullable|string|max:20',
            'company_address'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'              => 'Nama Lengkap wajib diisi',
            'username.required'          => 'Username wajib diisi',
            'username.unique'            => 'Username sudah digunakan',
            'email.required'             => 'Email wajib diisi',
            'email.email'                => 'Email tidak valid',
            'email.unique'               => 'Email sudah terdaftar',
            'password.required'          => 'Kata Sandi wajib diisi',
            'password.min'               => 'Kata Sandi minimal 4 karakter',
            'company_name.required'      => 'Nama Perusahaan wajib diisi',
            'business_category.required' => 'Kategori Usaha wajib dipilih',
            'business_category.in'       => 'Kategori Usaha tidak valid',
            'company_email.required'     => 'Email Perusahaan wajib diisi',
            'company_email.email'        => 'Email Perusahaan tidak valid',
            'company_email.unique'       => 'Email Perusahaan sudah terdaftar',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'data'    => [],
                'message' => $validator->errors()->first(),
            ], 422)
        );
    }
}
