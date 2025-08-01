<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'client_id',
        'ticket_number',
        'subject',
        'message',
        'status',
        'priority',
        'admin_notes',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }
}
