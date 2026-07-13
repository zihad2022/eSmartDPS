<?php

namespace App\Http\Requests\Admin;

use App\Enums\Package\BillingCycle;
use App\Enums\Package\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    protected function prepareForValidation(): void
    {
        $hasTrial = $this->boolean('has_trial');
        $discountValue = $this->filled('discount_value') ? (int) $this->input('discount_value') : 0;

        $this->merge([
            'discount_value' => $discountValue,
            'discount_type' => $discountValue > 0 ? $this->input('discount_type') : null,
            'user_limit' => $this->filled('user_limit') ? $this->input('user_limit') : 0,
            'has_trial' => $hasTrial,
            'trial_days' => $hasTrial ? ($this->input('trial_days') ?: 0) : 0,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $isPercentage = (int) $this->input('discount_type') === DiscountType::PERCENT->value;

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'integer', 'min:0'],
            'discount_value' => array_values(array_filter([
                'nullable', 'integer', 'min:0', $isPercentage ? 'max:100' : null,
            ])),
            'discount_type' => [Rule::requiredIf(fn (): bool => (int) $this->input('discount_value') > 0), 'nullable', Rule::enum(DiscountType::class)],
            'billing_cycle' => ['required', Rule::enum(BillingCycle::class)],
            'member_limit' => ['required', 'integer', 'min:0'],
            'user_limit' => ['required', 'integer', 'min:0'],
            'project_limit' => ['required', 'integer', 'min:0'],
            'has_trial' => ['required', 'boolean'],
            'trial_days' => ['required', 'integer', Rule::when($this->boolean('has_trial'), ['min:1'], ['min:0']), 'max:365'],
            'is_active' => ['required', 'boolean'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'boolean'],
        ];
    }
}
