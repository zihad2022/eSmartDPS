<?php

namespace App\Models;

use App\Enums\Ledger\LedgerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ledger Model
 *
 * Represents a financial record (transaction) for a client.
 * Each ledger entry belongs to a category (income/expense type) and a client.
 */
class Ledger extends Model
{
    /*--------------------------------
    | MASS ASSIGNABLE
    --------------------------------*/
    // Fields that can be mass-assigned (used in create/update)
    protected $fillable = [
        'ledger_category_id', // Link to the category of this ledger (e.g. income, expense, salary)
        'client_id',          // The client this ledger entry belongs to
        'type',               // Type of ledger (defined in LedgerType enum: e.g., debit/credit)
        'description',        // Short description of the transaction
        'amount',             // Amount of money involved in this entry
        'entry_date',         // Date when the entry was recorded
        'notes',              // Any additional notes/details
    ];

    /*--------------------------------
    | CASTS
    --------------------------------*/
    protected $casts = [
        'entry_date' => 'datetime',   // Automatically convert entry_date to Carbon instance
        'type' => LedgerType::class,  // Cast type field to Enum (LedgerType)
    ];

    /*--------------------------------
    | RELATIONSHIPS
    --------------------------------*/

    /**
     * Each ledger belongs to a category
     * Example: Income, Expense, Salary, Rent, etc.
     */
    public function ledgerCategory(): BelongsTo
    {
        return $this->belongsTo(LedgerCategory::class);
    }

    /**
     * Each ledger belongs to a client
     * Example: which client this income/expense is associated with
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
