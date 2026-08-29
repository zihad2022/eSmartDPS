<?php

namespace App\Models;

use App\Domain\Clients\Models\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    use HasFactory;
    protected $fillable = [
        'ticket_id',
        'client_id',
        'admin_id',
        'message',
        'attachment',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function isFromAdmin(): bool
    {
        return $this->admin_id !== null;
    }

    public function isFromClient(): bool
    {
        return $this->client_id !== null;
    }
}
