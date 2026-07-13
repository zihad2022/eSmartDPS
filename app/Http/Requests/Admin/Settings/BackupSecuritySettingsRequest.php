<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BackupSecuritySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check() && auth('admin')->user()->can('edit settings');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'backup_enabled' => $this->boolean('backup_enabled'),
        ]);
    }

    public function rules(): array
    {
        return [
            'backup_enabled' => ['required', 'boolean'],
            'backup_frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly'])],
            'session_timeout_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
        ];
    }
}
