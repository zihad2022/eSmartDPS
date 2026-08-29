<?php

namespace App\Http\Requests\Member;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('member')->check();
    }

    public function rules(): array
    {
        $methodValues = array_map(fn ($m) => $m->value, PaymentMethod::cases());

        return [
            'payment_amount' => ['required', 'numeric', 'min:1'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in($methodValues)],
            'reference_number' => ['nullable', 'string', 'max:64'],
            'payment_notes' => ['nullable', 'string', 'max:1000'],
            'receipt_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
