<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:255'],
            'site_slogan' => ['nullable', 'string', 'max:255'],
            'site_description' => ['nullable', 'string'],
            'site_keywords' => ['nullable', 'string'],
            'meta_codes' => ['nullable', 'string'],
            'site_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:1024'],
            'graph_thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ];
    }
}
