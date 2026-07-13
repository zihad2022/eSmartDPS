<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class UpdatePaymentSettingsAction
{
    public function execute(AdminSetting $settings, string $section, array $data): AdminSetting
    {
        $allowed = [
            'general' => ['currency', 'late_fee'],
            'bkash' => [
                'bkash_base_url', 'bkash_username', 'bkash_password', 'bkash_app_key',
                'bkash_app_secret', 'bkash_charge', 'bkash_status',
            ],
            'sslcommerz' => ['sslcommerz_store_id', 'sslcommerz_store_password', 'sslcommerz_mode'],
        ];

        if (! isset($allowed[$section])) {
            throw ValidationException::withMessages([
                'section' => ['The selected payment settings section is invalid.'],
            ]);
        }

        $payload = collect($data)->only($allowed[$section])->all();

        foreach (['bkash_password', 'bkash_app_secret', 'sslcommerz_store_password'] as $secret) {
            if (array_key_exists($secret, $payload) && blank($payload[$secret])) {
                unset($payload[$secret]);
            }
        }

        $settings->update($payload);
        Cache::forget('admin_settings_first');
        ActivityLogger::log(ucfirst($section).' payment settings updated.');

        return $settings->refresh();
    }
}
