<?php

namespace App\Models;

use App\Domain\Clients\Models\Client;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'ticket_number',
        'subject',
        'message',
        'status',
        'priority',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::OPEN);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::IN_PROGRESS);
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::RESOLVED);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::CLOSED);
    }
}
