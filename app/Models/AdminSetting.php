<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdminSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_slogan',
        'site_description',
        'site_keywords',
        'meta_codes',
        'site_logo',
        'favicon',
        'graph_thumbnail',
        'helpline_number',
        'email_address',
        'office_address',
        'google_map',
        'facebook_page',
        'facebook_group',
        'whatsapp_channel',
        'telegram_channel',
        'linkedin',
        'twitter_x',
        'youtube',
        'tiktok',
        'currency',
        'late_fee',
        'bkash_base_url',
        'bkash_username',
        'bkash_password',
        'bkash_app_key',
        'bkash_app_secret',
        'bkash_charge',
        'bkash_status',
        'sslcommerz_store_id',
        'sslcommerz_store_password',
        'sslcommerz_mode',
        'sms_api_key',
        'sms_client_id',
        'sms_sender_id',
        'sms_api_url',
        'sms_balance_api',
        'sms_message_template',
        'sms_status',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'email_message_template',
        'backup_enabled',
        'backup_frequency',
        'session_timeout_minutes',
        'last_backup_at',
        'last_backup_path',
    ];

    protected $hidden = [
        'bkash_password',
        'bkash_app_secret',
        'sslcommerz_store_password',
        'sms_api_key',
        'mail_password',
    ];

    protected function casts(): array
    {
        return [
            'late_fee' => 'integer',
            'bkash_charge' => 'decimal:2',
            'bkash_status' => 'boolean',
            'sms_status' => 'boolean',
            'mail_port' => 'integer',
            'backup_enabled' => 'boolean',
            'session_timeout_minutes' => 'integer',
            'last_backup_at' => 'datetime',
        ];
    }

    protected function siteLogoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->site_logo ? Storage::url($this->site_logo) : null);
    }

    protected function faviconUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->favicon ? Storage::url($this->favicon) : null);
    }

    protected function graphThumbnailUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->graph_thumbnail ? Storage::url($this->graph_thumbnail) : null);
    }
}
