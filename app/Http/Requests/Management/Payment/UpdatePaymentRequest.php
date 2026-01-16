<?php

namespace App\Http\Requests\Management\Payment;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
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
            'id' => 'required|exists:payment_methods,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank_transfer,e-wallet,other',
        ];
    }
}
