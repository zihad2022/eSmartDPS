<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PakageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            'price' => 'required|numeric|min:0',
            'discount_value' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'required',
            'billing_cycle' => 'required',

            'member_limit' => 'required|integer|min:0',
            'user_limit' => 'required|integer|min:0',
            'project_limit' => 'required|integer|min:0',

            'has_trial' => 'required|boolean',
            'trial_days' => 'required_if:has_trial,1|integer|min:0',

            'is_active' => 'required|boolean',
        ];
    }
}
