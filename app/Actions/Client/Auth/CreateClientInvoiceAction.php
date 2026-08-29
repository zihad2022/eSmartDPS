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
     * @param  array{billing_start?:mixed,billing_end?:mixed,due_date?:mixed,amount?:int}  $dates
     */
    public function execute(Client $client, Package $package, array $dates = []): Invoice
    {
        $billingStart = $dates['billing_start'] ?? now();
        $billingEnd = $dates['billing_end'] ?? $package->billingEndDate($billingStart);

        return $this->createInvoiceAction->execute(
            client: $client,
            package: $package,
            billingStart: $billingStart,
            billingEnd: $billingEnd,
            dueDate: $dates['due_date'] ?? null,
            amount: $dates['amount'] ?? null,
        );
    }
}
