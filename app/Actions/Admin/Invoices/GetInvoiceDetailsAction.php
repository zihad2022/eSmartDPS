<?php

namespace App\Actions\Admin\Invoices;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Domain\Invoices\Models\Invoice;

class GetInvoiceDetailsAction
{
    public function __construct(
        private readonly GetAdminSettingsAction $getSettings,
    ) {}

    public function execute(Invoice $invoice): array
    {
        return [
            'invoice' => $invoice->load(['client', 'package']),
            'settings' => $this->getSettings->execute(),
        ];
    }
}
