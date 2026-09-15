<?php

namespace App\Http\Controllers\Client;

use App\Actions\Invoices\GenerateInvoiceAction;
use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Http\Request;

class SubscriptionPaymentController extends Controller
{
    /**
     * Show payment method selection for existing invoice.
     */
    public function selectMethod(Invoice $invoice)
    {
        if ($invoice->status !== InvoiceStatus::UNPAID) {
            return redirect()->route('client.invoices.index')
                ->with('info', 'This invoice is already paid.');
        }

        $settings = AdminSetting::first();

        $paymentMethods = $this->availableMethods($settings);

        return view('client.payments.select-method', compact('invoice', 'paymentMethods', 'settings'));
    }

    /**
     * Start package subscription → generate invoice first, then go to select method.
     */
    public function startPackage(Package $package, Request $request, GenerateInvoiceAction $createInvoice)
    {
        $client = Client::findOrFail(owner_client_id());
        $billingStart = now()->startOfDay();
        $billingEnd = $package->billingEndDate($billingStart);

        $invoice = $createInvoice->execute(
            client: $client,
            package: $package,
            billingStart: $billingStart,
            billingEnd: $billingEnd,
            dueDate: now()->addDays(7),
        );

        return redirect()->route('client.payments.select', $invoice->id);
    }

    /**
     * Process the selected payment method.
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_method' => 'required|in:bkash,sslcommerz',
        ]);

        if ($invoice->status !== InvoiceStatus::UNPAID) {
            return redirect()->route('client.invoices.index')
                ->with('info', 'This invoice is already paid.');
        }

        switch ($request->payment_method) {
            case 'bkash':
                // Directly trigger bKash payment logic
                return app(BkashPaymentController::class)->pay($invoice);
            case 'sslcommerz':
                // Directly trigger SSLCommerz payment logic
                return app(SslcommerzPaymentController::class)->pay($invoice);
        }

        return back()->with('error', 'Invalid payment method selected.');
    }

    /**
     * Helper: available payment methods based on system settings
     */
    private function availableMethods(?AdminSetting $settings = null): array
    {
        $settings ??= AdminSetting::first();
        $methods = [];

        if ($settings?->bkash_status ?? true) {
            $methods['bkash'] = 'bKash';
        }

        if (filled($settings?->sslcommerz_store_id) || filled(config('payments.sslcommerz.store_id'))) {
            $methods['sslcommerz'] = 'SSLCommerz';
        }

        if (empty($methods)) {
            $methods['bkash'] = 'bKash';
        }

        return $methods;
    }
}
