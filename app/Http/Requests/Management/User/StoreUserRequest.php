<?php

namespace App\Http\Requests\Management\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek akses ke menu Manajemen User (code: MJ-01)
        return Gate::allows('access-menu', 'MJ-01');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->input('id');

        return [
            'id'        => 'nullable|exists:users,id',
            'company'   => 'required',
            'username'  => [
                'required',
                Rule::unique('users', 'username')
                    ->ignore($userId)
                    ->where(function ($query) {
                        return $query->where('status', 1);
                    }),
            ],
            'email'     => [
                'required',
                'email',
                Rule::unique('users', 'email')
                    ->ignore($userId)
                    ->where(function ($query) {
                        return $query->where('status', 1);
                    }),
            ],
            'name'      => 'required',
            'paket'     => 'required',
        ];
    }

    public function messages()
    {
        return [
            'company.required'  => 'Perusahaan harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique'   => 'Username sudah digunakan oleh user aktif lain',
            'email.required'    => 'Email harus diisi',
            'email.email'       => 'Email tidak valid',
            'email.unique'      => 'Email sudah digunakan oleh user aktif lain',
            'name.required'     => 'Nama harus diisi',
            'paket.required'    => 'Paket harus diisi',
        ];
    }
}