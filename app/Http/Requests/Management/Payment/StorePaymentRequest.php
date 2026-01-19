<?php

namespace App\Http\Requests\Management\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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

        return [
            'id'    => 'nullable|exists:payment_methods,id',
            'name'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('payment_methods', 'name')
                    ->ignore($paymentId)
                    ->where(function ($query) {
                        return $query->where('status', 1);
                    }),
            ],
            'type'  => 'required|in:cash,bank_transfer,e-wallet,other',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.string'   => 'Nama harus berupa string.',
            'name.max'      => 'Nama tidak boleh lebih dari 255 karakter.',
            'name.unique'   => 'Nama metode pembayaran sudah terdaftar.',
            'type.required' => 'Tipe pembayaran wajib diisi.',
            'type.in'       => 'Tipe pembayaran tidak valid.',
        ];
    }
}
