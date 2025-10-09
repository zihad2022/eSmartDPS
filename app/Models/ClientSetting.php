<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSetting extends Model
{
    protected $fillable = [
        // Belongs to Client
        'client_id',

        // General Settings
        'organization_name',
        'short_name',
        'contact_email',
        'contact_phone',
        'address',
        'currency',

        // Share Settings
        'share_price',
        'minimum_shares',
        'maximum_shares',
        'share_transfer_fee',
        'allow_partial_shares',

        // Payment Settings
        'payment_due_date',
        'late_payment_fee',
        'grace_period_days',
        'payment_methods',

        // Notification Settings
        'sms_api_provider',
        'sms_api_key',
        'email_payment_confirmations',
        'email_payment_reminders',
        'email_payment_reports',
        'sms_payment_confirmations',
        'sms_payment_reminders',

        // Backup & Security
        'auto_backup',
        'two_factor_auth',
        'session_timeout',
        'login_notifications',
    ];

    protected $casts = [
        'payment_methods' => 'array',
        'allow_partial_shares' => 'boolean',
        'email_payment_confirmations' => 'boolean',
        'email_payment_reminders' => 'boolean',
        'email_payment_reports' => 'boolean',
        'sms_payment_confirmations' => 'boolean',
        'sms_payment_reminders' => 'boolean',
        'auto_backup' => 'boolean',
        'two_factor_auth' => 'boolean',
        'session_timeout' => 'boolean',
        'login_notifications' => 'boolean',
    ];
}
