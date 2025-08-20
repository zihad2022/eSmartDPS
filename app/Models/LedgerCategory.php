<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * LedgerCategory Model
 *
 * Represents a category/group for ledger entries.
 * Example categories: "Income", "Expense", "Salary", "Office Rent", etc.
 *
 * Each category belongs to a client (so categories can be client-specific)
 * and has many ledger entries assigned under it.
 */
class LedgerCategory extends Model
{
    /*--------------------------------
    | MASS ASSIGNABLE
    --------------------------------*/
    // Fields that can be mass-assigned (used in create/update)
    protected $fillable = [
        'client_id',     // The client who owns this category
        'name',          // Category name (e.g., Income, Expense, Rent, Salary)
        'description',   // Optional description about the category
    ];

    /*--------------------------------
    | RELATIONSHIPS
    --------------------------------*/

    /**
     * Each category belongs to one client
     * Example: Client A can have their own "Expense" category,
     *          Client B can have a different set of categories.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * A category can have many ledger entries
     * Example: "Expense" category might have multiple expense records.
     */
    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class);
    }
}
