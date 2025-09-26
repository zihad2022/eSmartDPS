<?php

namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SSLCommerzPaymentController extends Controller
{
    protected $storeId;
    protected $storePassword;
    protected $sandbox;

    public function __construct()
    {
        $this->storeId = config('payments.sslcommerz.store_id');
        $this->storePassword = config('payments.sslcommerz.store_password');
        $this->sandbox = config('payments.sslcommerz.sandbox', true);
    }

    /**
     * Step 1: Redirect client to SSLCommerz hosted checkout
     */
    public function pay(Invoice $invoice)
    {
        if ($invoice->status === \App\Enums\InvoiceStatus::PAID) {
            return redirect()->route('client.invoices.index')
                ->with('info', 'Invoice already paid.');
        }

        $url = $this->sandbox
            ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
            : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';

        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $invoice->invoice_amount,
            'currency' => $invoice->currency ?? 'BDT',
            'tran_id' => uniqid('ssl_'),
            'success_url' => route('client.payments.sslcommerz.success', $invoice->id),
            'fail_url' => route('client.payments.sslcommerz.fail', $invoice->id),
            'cancel_url' => route('client.payments.sslcommerz.cancel', $invoice->id),
            'cus_name' => $invoice->client->full_name,
            'cus_email' => $invoice->client->email ?? 'noemail@example.com',
            'cus_phone' => $invoice->client->phone,
            'cus_add1' => $invoice->client->address ?? 'N/A',
            'ship_country' => 'Bangladesh',
            'product_name' => $invoice->package_name ?? 'Subscription',
            'product_category' => 'Subscription',
            'product_profile' => 'general',
        ];

        $response = Http::asForm()->post($url, $postData)->json();

        if (isset($response['GatewayPageURL'])) {
            // Save transaction id in invoice for verification later
            $invoice->update([
                'payment_reference' => $postData['tran_id'],
            ]);
            return redirect()->away($response['GatewayPageURL']);
        }

        return back()->with('error', 'Unable to connect to SSLCommerz gateway.');
    }

    /**
     * Step 2: Success Callback
     */
    public function success(Request $request, Invoice $invoice, PackageService $packageService)
    {
        // Validate the response with SSLCommerz validation API
        if ($this->validateTransaction($request->all())) {
            $invoice->update([
                'status' => \App\Enums\InvoiceStatus::PAID,
                'paid_at' => now(),
                'payment_method' => 'SSLCommerz',
                'trx_id' => $request->tran_id,
            ]);

            // Renew package
            $client = $invoice->client;
            $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;
            if ($clientPackage && $clientPackage->package) {
                $packageService->renewSubscription($client, $clientPackage->package);
            }

            return redirect()->route('client.dashboard')
                ->with('success', 'Payment successful via SSLCommerz!');
        }

        return redirect()->route('client.invoices.index')->with('error', 'Payment validation failed.');
    }

    /**
     * Step 3: Fail Callback
     */
    public function fail(Invoice $invoice)
    {
        return redirect()->route('client.invoices.index')->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Step 4: Cancel Callback
     */
    public function cancel(Invoice $invoice)
    {
        return redirect()->route('client.invoices.index')->with('info', 'Payment was cancelled.');
    }

    /**
     * Helper: Validate Transaction with SSLCommerz API
     */
    protected function validateTransaction($params)
    {
        $url = $this->sandbox
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

        $response = Http::get($url, [
            'val_id' => $params['val_id'] ?? null,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'v' => 1,
            'format' => 'json',
        ])->json();

        return isset($response['status']) && $response['status'] === 'VALID';
    }
}