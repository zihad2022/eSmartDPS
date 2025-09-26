<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UddoktapayController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('payments.uddoktapay.api_key');
        $this->baseUrl = config('payments.uddoktapay.base_url', 'https://api.uddoktapay.com/api');
    }

    /**
     * Step 1: Initiate payment request
     */
    public function pay(Invoice $invoice)
    {
        if ($invoice->status === \App\Enums\InvoiceStatus::PAID) {
            return redirect()->route('client.invoices.index')->with('info', 'Invoice already paid.');
        }

        $payload = [
            'amount'      => $invoice->invoice_amount,
            'currency'    => 'BDT',
            'invoice_id'  => $invoice->invoice_number,
            'success_url' => route('client.payments.uddoktapay.callback', ['invoice' => $invoice->id]),
            'cancel_url'  => route('client.invoices.index'),
            'fail_url'    => route('client.invoices.index'),
            'customer'    => [
                'name'  => $invoice->client->name,
                'email' => $invoice->client->email,
                'phone' => $invoice->client->phone,
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ])->post("{$this->baseUrl}/checkout", $payload);

        $result = $response->json();

        if (!isset($result['payment_url'])) {
            return back()->with('error', 'Unable to initiate UddoktaPay payment.');
        }

        // Store reference for verification later
        $invoice->update([
            'payment_reference' => $result['transaction_id'] ?? null,
        ]);

        // Redirect to UddoktaPay checkout
        return redirect($result['payment_url']);
    }

    /**
     * Step 2: Handle callback after payment
     */
    public function callback(Request $request, Invoice $invoice, PackageService $packageService)
    {
        // Verify with UddoktaPay API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ])->get("{$this->baseUrl}/verify/{$invoice->payment_reference}");

        $verification = $response->json();

        if (!isset($verification['status']) || $verification['status'] !== 'success') {
            return redirect()->route('client.invoices.index')
                ->with('error', 'Payment verification failed.');
        }

        // -----------------------------
        // Update Invoice
        // -----------------------------
        $invoice->update([
            'status'         => \App\Enums\InvoiceStatus::PAID,
            'paid_at'        => now(),
            'payment_method' => 'UddoktaPay',
            'trx_id'         => $verification['transaction_id'] ?? null,
        ]);

        // -----------------------------
        // Renew Subscription
        // -----------------------------
        $client = $invoice->client;
        $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;

        if ($clientPackage && $clientPackage->package) {
            $packageService->renewSubscription($client, $clientPackage->package);
        }

        return redirect()->route('client.dashboard')->with('success', 'Payment successful via UddoktaPay!');
    }
}   