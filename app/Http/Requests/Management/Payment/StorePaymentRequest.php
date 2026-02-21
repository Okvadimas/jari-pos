<?php

namespace App\Http\Requests\Management\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cek akses ke menu Manajemen Payment (code: MJ-04)
        return Gate::allows('access-menu', 'MJ-04');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $paymentId = $this->input('id');
        $companyId = Auth::user()->company_id;

        return [
            'id'    => 'nullable|exists:payment_methods,id',
            'name'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods', 'name')
                    ->ignore($paymentId)
                    ->where('company_id', $companyId)
                    ->whereNull('deleted_at'),
            ],
            'type'  => 'required|in:cash,bank_transfer,e-wallet,other',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama metode pembayaran wajib diisi.',
            'name.string'   => 'Nama metode pembayaran harus berupa string.',
            'name.max'      => 'Nama metode pembayaran tidak boleh lebih dari 255 karakter.',
            'name.unique'   => 'Nama metode pembayaran sudah terdaftar.',
            'type.required' => 'Tipe metode pembayaran wajib diisi.',
            'type.in'       => 'Tipe metode pembayaran tidak valid.',
        ];
    }
}
