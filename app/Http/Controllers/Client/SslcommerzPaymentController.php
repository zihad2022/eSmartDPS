<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Invoice;
use App\Services\PackageService;
use App\Services\Payments\SslcommerzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SslcommerzPaymentController extends Controller
{
    protected SslcommerzService $ssl;

    public function __construct(SslcommerzService $ssl)
    {
        $this->ssl = $ssl;
    }

    /**
     * Redirect client to SSLCommerz hosted checkout.
     */
    public function pay(Invoice $invoice)
    {
        if (! (bool) (AdminSetting::query()->value('sslcommerz_status') ?? false)) {
            return back()->with('error', 'SSLCommerz payment is currently unavailable.');
        }

        if ($invoice->status === InvoiceStatus::PAID) {
            return redirect()->route('client.invoices.index')
                ->with('info', 'Invoice already paid.');
        }

        $result = $this->ssl->initiate($invoice);

        if (! $result['ok']) {
            return back()->with('error', $result['error'] ?? 'Unable to connect to SSLCommerz gateway.');
        }

        // Persist the generated transaction id to the invoice for reference
        if (! empty($result['request']['tran_id'])) {
            $invoice->update([
                'payment_reference' => $result['request']['tran_id'],
            ]);
        }

        return redirect()->away($result['url']);
    }

    /**
     * Handle SSLCommerz success callback.
     */
    public function success(Request $request, Invoice $invoice, PackageService $packageService)
    {
        if (! $this->ssl->validate($request->all())) {
            return redirect()->route('client.invoices.index')
                ->with('error', 'Payment validation failed.');
        }

        $invoice->update([
            'status' => InvoiceStatus::PAID,
            'paid_at' => now(),
            'payment_method' => PaymentMethod::ONLINE,
            'trx_id' => $request->tran_id,
        ]);

        // Activate/schedule the exact billing period represented by this invoice.
        $client = $invoice->client;
        if ($invoice->package) {
            $packageService->activateSubscriptionFromInvoice($client, $invoice);
        }

        // Ensure the client is authenticated in this browser session.
        // Cross-site POST callbacks may not include cookies due to SameSite policies ("lax").
        // Since the transaction is validated above, it's safe to authenticate the invoice owner.
        if (! Auth::guard('client')->check() || Auth::guard('client')->id() !== $client->id) {
            Auth::guard('client')->loginUsingId($client->id);
            // Regenerate session ID to prevent fixation and persist the login.
            $request->session()->regenerate();
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Payment successful via SSLCommerz!');
    }

    /**
     * Handle failed payment callback.
     */
    public function fail(Invoice $invoice)
    {
        return redirect()->route('client.invoices.index')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Handle cancelled payment callback.
     */
    public function cancel(Invoice $invoice)
    {
        return redirect()->route('client.invoices.index')
            ->with('info', 'Payment was cancelled.');
    }

    // Validation is delegated to SslcommerzService
}
