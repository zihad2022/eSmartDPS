<?php

namespace App\Http\Controllers\Client;

use App\Actions\Invoices\GenerateInvoiceAction;
use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
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
        $validated = $request->validate([
            'payment_method' => ['nullable', 'in:bkash,sslcommerz'],
        ]);

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

        $paymentMethod = $validated['payment_method'] ?? null;

        if ($paymentMethod) {
            $settings = AdminSetting::first();

            if ($paymentMethod === 'bkash') {
                $bkashActive = $settings?->bkash_status ?? true;
                if (! $bkashActive) {
                    return back()->with('error', 'bKash payment is currently unavailable.');
                }

                return app(BkashPaymentController::class)->pay($invoice);
            }

            if ($paymentMethod === 'sslcommerz') {
                $sslActive = filled($settings?->sslcommerz_store_id) || filled(config('payments.sslcommerz.store_id'));
                if (! $sslActive) {
                    return back()->with('error', 'SSLCommerz payment is currently unavailable.');
                }

                return app(SslcommerzPaymentController::class)->pay($invoice);
            }
        }

        return redirect()->route('client.subscription.packages')
            ->with('error', 'Please select a payment method from the subscription popup.');
    }
}
