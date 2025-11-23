<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Domain\Invoices\Models\Invoice;
use App\Domain\Clients\Services\PackageService;
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
        if ($invoice->status === \App\Enums\InvoiceStatus::PAID) {
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
            'status'         => \App\Enums\InvoiceStatus::PAID,
            'paid_at'        => now(),
            'payment_method' => PaymentMethod::ONLINE,
            'trx_id'         => $request->tran_id,
        ]);

        // Renew client’s subscription
        $client = $invoice->client;
        $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;

        if ($clientPackage && $clientPackage->package) {
            $packageService->renewSubscription($client, $clientPackage->package);
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
