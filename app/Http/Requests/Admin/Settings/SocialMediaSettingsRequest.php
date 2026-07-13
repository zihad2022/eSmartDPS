<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class SocialMediaSettingsRequest extends FormRequest
{
    public function authorize(): bool { return auth('admin')->check(); }

    public function rules(): array
    {
        return [
            'facebook_page' => ['nullable', 'url', 'max:500'],
            'facebook_group' => ['nullable', 'url', 'max:500'],
            'whatsapp_channel' => ['nullable', 'url', 'max:500'],
            'telegram_channel' => ['nullable', 'url', 'max:500'],
            'linkedin' => ['nullable', 'url', 'max:500'],
            'twitter_x' => ['nullable', 'url', 'max:500'],
            'youtube' => ['nullable', 'url', 'max:500'],
            'tiktok' => ['nullable', 'url', 'max:500'],
        ];
    }
}
