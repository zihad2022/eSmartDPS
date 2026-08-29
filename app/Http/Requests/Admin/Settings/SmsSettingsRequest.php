<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SmsSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'sms_api_key' => ['nullable', 'string'],
            'sms_client_id' => ['nullable', 'string', 'max:255'],
            'sms_sender_id' => ['nullable', 'string', 'max:255'],
            'sms_api_url' => ['nullable', 'url', 'max:1000'],
            'sms_balance_api' => ['nullable', 'url', 'max:1000'],
            'sms_message_template' => ['nullable', 'string', 'max:5000'],
            'sms_status' => ['required', 'boolean'],
        ];
    }
}
