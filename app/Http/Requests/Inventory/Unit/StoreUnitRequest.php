<?php

namespace App\Http\Requests\Inventory\Unit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StoreUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek akses ke menu Inventori Unit (code: IN-01)
        return Gate::allows('access-menu', 'IN-01');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $unitId = $this->input('id');
        $companyId = Auth::user()->company_id;

        return [
            'id'    => 'nullable|exists:units,id',
            'code'  => [
                'required',
                'string',
                'max:10',
                Rule::unique('units', 'code')
                    ->ignore($unitId)
                    ->where('company_id', $companyId)
                    ->whereNull('deleted_at'),
            ],
            'name'  => [
                'required',
                'string',
                'max:50',
                Rule::unique('units', 'name')
                    ->ignore($unitId)
                    ->where('company_id', $companyId)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode satuan wajib diisi.',
            'code.string'   => 'Kode satuan harus berupa string.',
            'code.max'      => 'Kode satuan tidak boleh lebih dari 10 karakter.',
            'code.unique'   => 'Kode satuan sudah terdaftar.',
            'name.required' => 'Nama satuan wajib diisi.',
            'name.string'   => 'Nama satuan harus berupa string.',
            'name.max'      => 'Nama satuan tidak boleh lebih dari 50 karakter.',
            'name.unique'   => 'Nama satuan sudah terdaftar.',
        ];
    }
}
