<?php

namespace App\Observers\Client;

use App\Models\Ledger;
use App\Services\ActivityLogger;

class LedgerObserver
{
    public function created(Ledger $ledger)
    {
        ActivityLogger::log("Ledger '{$ledger->type->label()}' was created.");
    }

    public function updated(Ledger $ledger)
    {
        ActivityLogger::log("Ledger '{$ledger->type->label()}' was updated.");
    }

    public function deleted(Ledger $ledger)
    {
        ActivityLogger::log("Ledger '{$ledger->type->label()}' was deleted.");
    }
}
