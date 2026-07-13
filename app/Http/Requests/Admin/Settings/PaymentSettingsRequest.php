<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentSettingsRequest extends FormRequest
{
    public function authorize(): bool { return auth('admin')->check(); }

    public function rules(): array
    {
        $section = $this->input('section');

        return match ($section) {
            'general' => [
                'section' => ['required', Rule::in(['general'])],
                'currency' => ['required', 'string', 'size:3'],
                'late_fee' => ['nullable', 'integer', 'min:0'],
            ],
            'bkash' => [
                'section' => ['required', Rule::in(['bkash'])],
                'bkash_base_url' => ['required', 'url', 'max:1000'],
                'bkash_username' => ['required', 'string', 'max:255'],
                'bkash_password' => ['nullable', 'string', 'max:1000'],
                'bkash_app_key' => ['required', 'string', 'max:1000'],
                'bkash_app_secret' => ['nullable', 'string', 'max:1000'],
                'bkash_charge' => ['required', 'numeric', 'min:0', 'max:100'],
                'bkash_status' => ['required', 'boolean'],
            ],
            'sslcommerz' => [
                'section' => ['required', Rule::in(['sslcommerz'])],
                'sslcommerz_store_id' => ['required', 'string', 'max:255'],
                'sslcommerz_store_password' => ['nullable', 'string', 'max:1000'],
                'sslcommerz_mode' => ['required', Rule::in(['live', 'sandbox'])],
            ],
            default => [
                'section' => ['required', Rule::in(['general', 'bkash', 'sslcommerz'])],
            ],
        };
    }
}
