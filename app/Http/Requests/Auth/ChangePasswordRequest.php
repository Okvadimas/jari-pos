<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required',
            'new_password'     => 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
            'password_confirmation' => 'required|same:new_password',
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'new_password.required'     => 'Kata sandi baru wajib diisi.',
            'new_password.min'          => 'Kata sandi baru minimal 8 karakter.',
            'new_password.regex'        => 'Kata sandi baru harus mengandung huruf besar, huruf kecil, dan angka.',
            'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
            'password_confirmation.same'     => 'Konfirmasi kata sandi tidak cocok dengan kata sandi baru.',
        ];
    }
}
