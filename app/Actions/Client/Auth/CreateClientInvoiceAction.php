<?php

namespace App\Actions\Client\Auth;

use App\Domain\Clients\Models\Client;
use App\Domain\Invoices\Actions\CreateInvoiceAction;
use App\Domain\Invoices\Models\Invoice;
use App\Domain\Packages\Models\Package;

class CreateClientInvoiceAction
{
    public function __construct(private readonly CreateInvoiceAction $createInvoiceAction) {}

    /**
     * @param  array{billing_start:mixed,billing_end:mixed,due_date?:mixed,amount?:int}  $dates
     */
    public function execute(Client $client, Package $package, array $dates): Invoice
    {
        return $this->createInvoiceAction->execute(
            client: $client,
            package: $package,
            billingStart: $dates['billing_start'],
            billingEnd: $dates['billing_end'],
            dueDate: $dates['due_date'] ?? null,
            amount: $dates['amount'] ?? null,
        );
    }
}
