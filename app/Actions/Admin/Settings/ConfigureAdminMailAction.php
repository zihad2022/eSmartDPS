<?php

namespace App\Actions\Admin\Settings;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class ConfigureAdminMailAction
{
    public function execute(?AdminSetting $settings = null): bool
    {
        $settings ??= app(GetAdminSettingsAction::class)->execute();

        if (blank($settings->mail_host) || blank($settings->mail_from_address)) {
            return false;
        }

        $scheme = $settings->mail_encryption === 'ssl' ? 'smtps' : null;

        Config::set([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.host' => $settings->mail_host,
            'mail.mailers.smtp.port' => $settings->mail_port ?: 587,
            'mail.mailers.smtp.username' => $settings->mail_username,
            'mail.mailers.smtp.password' => $settings->mail_password,
            'mail.from.address' => $settings->mail_from_address,
            'mail.from.name' => $settings->mail_from_name ?: $settings->site_name ?: config('app.name'),
        ]);

        Mail::purge('smtp');

        return true;
    }
}
