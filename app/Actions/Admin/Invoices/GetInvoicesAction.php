<?php

namespace App\Actions\Admin\Invoices;

use App\Actions\Admin\Settings\GetAdminSettingsAction;
use App\Enums\InvoiceStatus;
use App\Models\Invoice;

class GetInvoicesAction
{
    public function __construct(
        private readonly GetAdminSettingsAction $getSettings,
    ) {}

    public function execute(?string $search, ?string $status, int $perPage = 10): array
    {
        $statusMap = [
            'unpaid' => InvoiceStatus::UNPAID,
            'paid' => InvoiceStatus::PAID,
            'refund-request' => InvoiceStatus::REFUND_REQUESTED,
            'refunded' => InvoiceStatus::REFUNDED,
            'cancelled' => InvoiceStatus::CANCELLED,
        ];

        $query = Invoice::query()
            ->with('client:id,first_name,last_name,email,phone')
            ->when(filled($search), function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('package_name', 'like', "%{$search}%")
                        ->orWhere('package_description', 'like', "%{$search}%")
                        ->orWhere('trx_id', 'like', "%{$search}%")
                        ->orWhere('payment_id', 'like', "%{$search}%")
                        ->orWhere('wallet_address', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search): void {
                            $clientQuery->where(function ($client) use ($search): void {
                                $client->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            });
                        });
                });
            })
            ->when(isset($statusMap[$status]), fn ($query) => $query->where('status', $statusMap[$status]))
            ->latest('id');

        $counts = Invoice::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'invoices' => $query->paginate($perPage)->withQueryString(),
            'totalInvoices' => (int) $counts->sum(),
            'totalPaidInvoices' => (int) ($counts[InvoiceStatus::PAID->value] ?? 0),
            'totalUnpaidInvoices' => (int) ($counts[InvoiceStatus::UNPAID->value] ?? 0),
            'totalRefundedInvoices' => (int) ($counts[InvoiceStatus::REFUNDED->value] ?? 0),
            'totalCancelledInvoices' => (int) ($counts[InvoiceStatus::CANCELLED->value] ?? 0),
            'totalRefundRequestedInvoices' => (int) ($counts[InvoiceStatus::REFUND_REQUESTED->value] ?? 0),
            'search' => $search,
            'currency' => $this->getSettings->execute()->currency ?: '$',
        ];
    }
}
