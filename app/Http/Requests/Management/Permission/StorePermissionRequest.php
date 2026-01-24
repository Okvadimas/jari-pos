<?php

namespace App\Http\Requests\Management\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class StorePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek akses ke menu Manajemen User (code: MJ-01)
        return Gate::allows('access-menu', 'MJ-02');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roleId = $this->input('id');

        return [
            'id'        => 'nullable|exists:roles,id',
            'name'      => [
                'required',
                Rule::unique('roles', 'name')
                    ->ignore($roleId)
                    ->whereNull('deleted_at'),
            ],
            'slug'     => [
                'required',
                Rule::unique('roles', 'slug')
                    ->ignore($roleId)
                    ->whereNull('deleted_at'),
            ],
            'menus'     => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'Nama harus diisi',
            'name.unique'       => 'Nama sudah digunakan',
            'slug.required'     => 'Slug harus diisi',
            'slug.unique'       => 'Slug sudah digunakan',
            'menus.required'    => 'Minimal harus memilih 1 menu',
        ];
    }
}