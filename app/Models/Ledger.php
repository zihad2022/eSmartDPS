<?php

namespace App\Models;

use App\Domain\Clients\Models\Client;
use App\Enums\Ledger\LedgerType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    protected $fillable = [
        'ledger_category_id',
        'client_id',
        'type',
        'description',
        'amount',
        'entry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'entry_date' => 'date',
            'type' => LedgerType::class,
        ];
    }

    public function ledgerCategory(): BelongsTo
    {
        return $this->belongsTo(LedgerCategory::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('type', LedgerType::INCOME);
    }

    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('type', LedgerType::EXPENSE);
    }
}
