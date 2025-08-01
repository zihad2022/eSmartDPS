<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'client_id',
        'invoice_number',
        'invoice_amount',
        'status',
        'payment_id',
        'trx_id',
        'payment_method',
        'wallet_address',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
