<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientSetting extends Model
{
    protected $fillable = [
        'client_id',
        'organization_name',
        'short_name',
        'contact_email',
        'contact_phone',
        'address',
        'currency',
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
