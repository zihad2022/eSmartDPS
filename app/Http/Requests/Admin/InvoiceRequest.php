<?php

namespace App\Http\Requests\Admin;

use App\Enums\InvoiceStatus;
use App\Domain\Invoices\Models\Invoice;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        /** @var Invoice|null $invoice */
        $invoice = $this->route('invoice');

        return [
            'invoice_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('invoices', 'invoice_number')->ignore($invoice?->id),
            ],
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->whereNull('parent_id'),
            ],
            'package_id' => ['required', 'integer', Rule::exists('packages', 'id')],
            'billing_start' => ['required', 'date'],
            'billing_end' => ['required', 'date', 'after:billing_start'],
            'due_date' => ['nullable', 'date', 'after_or_equal:billing_start'],
            'invoice_amount' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::enum(InvoiceStatus::class)],
            'payment_id' => ['nullable', 'string', 'max:100'],
            'trx_id' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', Rule::enum(PaymentMethod::class)],
            'wallet_address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
