<?php

namespace App\Http\Controllers\Client;

use App\Actions\Invoices\GenerateInvoiceAction;
use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;
use App\Services\PackageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StartPaidSubscriptionController extends Controller
{
    public function __construct(
        private readonly PackageService $packageService,
        private readonly GenerateInvoiceAction $generateInvoiceAction
    ) {}

    /**
     * Handle starting a paid subscription for a client.
     */
    public function __invoke(Request $request, Package $package): RedirectResponse
    {
        $client = Client::findOrFail(owner_client_id());

        if ($error = $this->packageService->validatePackageSwitch($client, $package)) {
            return back()->with('error', $error);
        }

        // If the package is free (price is 0), activate immediately
        if ((int) $package->price === 0) {
            $this->packageService->renewSubscription($client, $package);

            return redirect()->route('client.dashboard')->with('success', 'Plan activated successfully.');
        }

        // For paid packages, find or generate an unpaid invoice and redirect to payment
        $billingStart = now()->startOfDay();
        $billingEnd = $package->billingEndDate($billingStart);

        $invoice = Invoice::query()
            ->where('client_id', $client->id)
            ->where('package_id', $package->id)
            ->where('status', InvoiceStatus::UNPAID)
            ->latest('id')
            ->first();

        if (! $invoice) {
            $invoice = $this->generateInvoiceAction->execute(
                client: $client,
                package: $package,
                billingStart: $billingStart,
                billingEnd: $billingEnd,
                dueDate: now()->addDays(7),
            );
        }

        return redirect()->route('client.payments.select', $invoice->id);
    }
}
