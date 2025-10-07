<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SslcommerzPaymentController extends Controller
{
    protected string $storeId;
    protected string $storePassword;
    protected bool $sandbox;

    public function __construct()
    {
        $this->storeId       = config('payments.sslcommerz.store_id', 'softc6610e80407051');
        $this->storePassword = config('payments.sslcommerz.store_password', 'softc6610e80407051@ssl');
        $this->sandbox       = config('payments.sslcommerz.sandbox', true);
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

        $url = $this->sandbox
            ? 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v3/api.php';

        $postData = [
            'store_id'      => $this->storeId,
            'store_passwd'  => $this->storePassword,
            'total_amount'  => $invoice->amount ?? $invoice->invoice_amount ?? 0,
            'currency'      => $invoice->currency ?? 'BDT',
            'tran_id'       => uniqid('ssl_'),
            'success_url'   => route('client.payments.sslcommerz.success', $invoice->id),
            'fail_url'      => route('client.payments.sslcommerz.fail', $invoice->id),
            'cancel_url'    => route('client.payments.sslcommerz.cancel', $invoice->id),

            'cus_name'      => $invoice->client->full_name ?? 'Customer',
            'cus_email'     => $invoice->client->email ?? 'customer@example.com',
            'cus_phone'     => $invoice->client->phone ?? '01700000000',
            'cus_add1'      => $invoice->client->address ?? 'Dhaka',
            'cus_city'      => 'Dhaka',
            'cus_country'   => 'Bangladesh',

            'ship_name'     => $invoice->client->full_name ?? 'Customer',
            'ship_add1'     => $invoice->client->address ?? 'Dhaka',
            'ship_city'     => 'Dhaka',
            'ship_postcode' => '1200',
            'ship_country'  => 'Bangladesh',

            'product_name'     => $invoice->package->name ?? 'Subscription',
            'product_category' => 'Subscription',
            'product_profile'  => 'general',
        ];

        $response = Http::asForm()->post($url, $postData)->json();

        if (! $response || empty($response['GatewayPageURL'])) {
            Log::error('SSLCommerz initialization failed', [
                'response' => $response,
                'request'  => $postData,
            ]);

            return back()->with('error', 'Unable to connect to SSLCommerz gateway.');
        }

        $invoice->update([
            'payment_reference' => $postData['tran_id'],
        ]);

        return redirect()->away($response['GatewayPageURL']);
    }

    /**
     * Handle SSLCommerz success callback.
     */
    public function success(Request $request, Invoice $invoice, PackageService $packageService)
    {
        if (! $this->validateTransaction($request->all())) {
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

    /**
     * Verify transaction with SSLCommerz validator API.
     */
    protected function validateTransaction(array $params): bool
    {
        $url = $this->sandbox
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

        $response = Http::get($url, [
            'val_id'       => $params['val_id'] ?? null,
            'store_id'     => $this->storeId,
            'store_passwd' => $this->storePassword,
            'v'            => 1,
            'format'       => 'json',
        ])->json();

        Log::info('SSLCommerz validation response', $response ?? []);

        return isset($response['status']) && $response['status'] === 'VALID';
    }
}
