<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'organization_name',
        'short_name',
        'contact_email',
        'contact_phone',
        'address',
        'currency',
        'share_price',
        'minimum_shares',
        'maximum_shares',
        'share_transfer_fee',
        'allow_partial_shares',
        'payment_due_date',
        'late_payment_fee',
        'grace_period_days',
        'payment_methods',
        'sms_api_provider',
        'sms_api_key',
        'email_payment_confirmations',
        'email_payment_reminders',
        'email_payment_reports',
        'sms_payment_confirmations',
        'sms_payment_reminders',
        'auto_backup',
        'two_factor_auth',
        'session_timeout',
        'login_notifications',
    ];

    protected function casts(): array
    {
        return [
            'share_price' => 'integer',
            'minimum_shares' => 'integer',
            'maximum_shares' => 'integer',
            'share_transfer_fee' => 'integer',
            'payment_due_date' => 'integer',
            'late_payment_fee' => 'integer',
            'grace_period_days' => 'integer',
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
