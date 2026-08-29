<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class ContactInfoSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'helpline_number' => ['nullable', 'string', 'max:30'],
            'email_address' => ['nullable', 'email', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:1000'],
            'google_map' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
