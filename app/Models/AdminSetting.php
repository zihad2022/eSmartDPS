<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdminSetting extends Model
{
    protected $fillable = [
        // ====== General Settings ======//
        'site_name',
        'site_slogan',
        'site_description',
        'site_keywords',
        'meta_codes',
        'site_logo',
        'favicon',
        'graph_thumbnail',

        /* ====== Contact Info ====== */
        'helpline_number',
        'email_address',
        'office_address',
        'google_map',

        /* ====== Social Media ====== */
        'facebook_page',
        'facebook_group',
        'whatsapp_channel',
        'telegram_channel',
        'linkedin',
        'twitter_x',
        'youtube',
        'tiktok',

        /* ====== Payment Settings ====== */
        'currency',
        'late_fee',

        // bKash Payment
        'bkash_app_key',
        'bkash_app_secret',
        'bkash_username',
        'bkash_password',

        // UddoktaPay
        'uddoktapay_api_key',
        'uddoktapay_secret',
        'uddoktapay_callback_url',

        // SSLCommerz
        'sslcommerz_store_id',
        'sslcommerz_store_password',
        'sslcommerz_mode',

        /* ====== SMS Settings ====== */
        'sms_api_key',
        'sms_client_id',
        'sms_sender_id',
        'sms_api_url',
        'sms_balance_api',
        'sms_message_template',

        /* ====== Email Settings ====== */
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'email_message_template',
    ];

    // protected $casts = [
    //     'late_fee' => 'float',
    // ];

    protected function siteLogoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->site_logo ? Storage::url($this->site_logo) : null,
        );
    }

    protected function faviconUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->favicon ? Storage::url($this->favicon) : null,
        );
    }

    protected function graphThumbnailUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->graph_thumbnail ? Storage::url($this->graph_thumbnail) : null,
        );
    }
}
