<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Invoice;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashPaymentController extends Controller
{
    protected $settings;
    protected $baseUrl;

    public function __construct()
    {
        $this->settings = AdminSetting::first();

        // Validate bKash settings enabled
        if (!$this->settings || !$this->settings->bkash_status) {
            abort(403, 'bKash payment is currently disabled.');
        }

        // Use base URL from DB (sandbox or live)
        $this->baseUrl = rtrim($this->settings->bkash_base_url, '/');
    }

    /**
     * Step 1: Redirect user to bKash checkout page
     */
    public function pay(Invoice $invoice)
    {
        if ($invoice->status === \App\Enums\InvoiceStatus::PAID) {
            return redirect()->route('client.invoices.index')
                ->with('info', 'Invoice already paid.');
        }

        Log::info("bKash: Starting payment for Invoice {$invoice->invoice_number}");

        $token = $this->getAccessToken();
        if (!$token) {
            return back()->with('error', 'Unable to connect to bKash.');
        }

        $amount = $invoice->invoice_amount;

        // Add extra charge if defined
        if ($this->settings->bkash_charge > 0) {
            $amount += $this->settings->bkash_charge;
        }

        $paymentResponse = $this->createPayment($invoice, $token, $amount);

        if (!isset($paymentResponse['paymentID'])) {
            Log::error("bKash: No paymentID for Invoice {$invoice->invoice_number}", $paymentResponse);
            return back()->with('error', 'Unable to initiate bKash payment.');
        }

        $invoice->update([
            'payment_reference' => $paymentResponse['paymentID'],
        ]);

        return redirect($paymentResponse['bkashURL'] ?? $paymentResponse['bkashCheckoutURL']);
    }

    /**
     * Step 2: Callback after payment attempt
     */
    public function callback(Request $request, PackageService $packageService)
    {
        $paymentID = $request->paymentID ?? $request->trx_id;

        $invoice = Invoice::where('payment_reference', $paymentID)->first();
        if (!$invoice) {
            return redirect()->route('client.invoices.index')
                ->with('error', 'Invalid payment reference.');
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return redirect()->route('client.invoices.index')
                ->with('error', 'Payment verification failed.');
        }

        $verification = $this->verifyPayment($paymentID, $token);

        if (($verification['status'] ?? null) !== 'Completed') {
            return redirect()->route('client.invoices.index')
                ->with('error', 'Payment not successful.');
        }

        $invoice->update([
            'status' => \App\Enums\InvoiceStatus::PAID,
            'paid_at' => now(),
            'payment_method' => 'bKash',
            'trx_id' => $verification['trxID'] ?? null,
        ]);

        // Renew subscription
        $client = $invoice->client;
        $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;
        if ($clientPackage && $clientPackage->package) {
            $packageService->renewSubscription($client, $clientPackage->package);
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Payment successful and subscription renewed!');
    }

    /**
     * Helper: Get Access Token
     */
    protected function getAccessToken()
    {
        try {
            $response = Http::withBasicAuth(
                $this->settings->bkash_app_key,
                $this->settings->bkash_app_secret
            )->post("{$this->baseUrl}/token/grant", [
                'username' => $this->settings->bkash_username,
                'password' => $this->settings->bkash_password,
            ]);

            $data = $response->json();
            Log::info("bKash Token Response", $data);

            return $data['id_token'] ?? null;
        } catch (\Exception $e) {
            Log::error("bKash Token Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper: Create Payment
     */
    protected function createPayment(Invoice $invoice, $token, $amount)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/payment/create", [
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $invoice->invoice_number,
                'callbackURL' => route('client.payments.bkash.callback'),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("bKash Create Payment Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper: Verify Payment
     */
    protected function verifyPayment($paymentID, $token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/payment/execute/{$paymentID}");

            return $response->json();
        } catch (\Exception $e) {
            Log::error("bKash Verify Payment Error: " . $e->getMessage());
            return [];
        }
    }
}
