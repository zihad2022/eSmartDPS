<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Invoice;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashPaymentController extends Controller
{
    private ?array $resolvedBkashConfig = null;

    /**
     * Step 1: Start payment (redirect to bKash checkout)
     */
    public function pay(Invoice $invoice)
    {
        if ($invoice->status === InvoiceStatus::PAID) {
            Log::warning('[bKash] Attempted to pay already paid invoice', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ]);

            return redirect()->route('client.invoices.index')
                ->with('info', 'Invoice already paid.');
        }

        Log::info('[bKash] Starting payment', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'amount' => $invoice->invoice_amount,
        ]);

        $token = $this->getAccessToken();
        if (! $token) {
            return back()->with('error', 'Unable to connect to bKash.');
        }

        $paymentResponse = $this->createPayment($invoice, $token, $invoice->invoice_amount);

        if (! isset($paymentResponse['paymentID']) || ! isset($paymentResponse['bkashURL'])) {
            Log::error('[bKash] CreatePayment failed', [
                'invoice_id' => $invoice->id,
                'response' => $paymentResponse,
            ]);

            return back()->with('error', 'Unable to initiate bKash payment: '.($paymentResponse['statusMessage'] ?? 'Unknown error'));
        }

        // Store bKash payment reference
        $invoice->update([
            'payment_reference' => $paymentResponse['paymentID'],
        ]);

        Log::info('[bKash] Payment initiated', [
            'invoice_id' => $invoice->id,
            'payment_reference' => $paymentResponse['paymentID'],
            'bkash_url' => $paymentResponse['bkashURL'],
        ]);

        // Redirect to bKash checkout
        return redirect($paymentResponse['bkashURL']);
    }

    /**
     * Step 2: Callback after payment approval
     */
    public function callback(Request $request, PackageService $packageService)
    {
        Log::info('[bKash Callback] Data received', $request->all());

        if ($request->status === 'cancel' || $request->has('cancel')) {
            Log::warning('[bKash Callback] Payment cancelled', $request->all());

            return redirect()->route('client.invoices.index')->with('info', 'Payment was cancelled.');
        }

        $paymentID = $request->paymentID ?? $request->trxID;
        if (! $paymentID) {
            Log::error('[bKash Callback] Missing paymentID/trxID', $request->all());

            return redirect()->route('client.invoices.index')->with('error', 'Missing payment reference.');
        }

        $invoice = Invoice::where('payment_reference', $paymentID)->first();
        if (! $invoice) {
            Log::error('[bKash Callback] No invoice found for payment_reference', [
                'payment_reference' => $paymentID,
            ]);

            return redirect()->route('client.invoices.index')->with('error', 'Invalid payment reference.');
        }

        $token = $this->getAccessToken();
        if (! $token) {
            Log::error('[bKash Callback] Token fetch failed for invoice', [
                'invoice_id' => $invoice->id,
            ]);

            return redirect()->route('client.invoices.index')->with('error', 'Payment verification failed.');
        }

        $verification = $this->executePayment($paymentID, $token);
        Log::info('[bKash ExecutePayment] Response', [
            'invoice_id' => $invoice->id,
            'verification' => $verification,
        ]);

        if (($verification['statusCode'] ?? null) === '0000' &&
            ($verification['transactionStatus'] ?? null) === 'Completed'
        ) {

            Log::info('[bKash Callback] Payment verified as completed');

            $invoice->update([
                'status' => InvoiceStatus::PAID,
                'paid_at' => now(),
                'payment_method' => PaymentMethod::ONLINE,
                'trx_id' => $verification['trxID'] ?? $paymentID,
            ]);

            Log::info('[bKash] Invoice marked as PAID', [
                'invoice_id' => $invoice->id,
                'trx_id' => $invoice->trx_id,
            ]);

            $client = $invoice->client;
            $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;

            if ($clientPackage && $clientPackage->package) {
                Log::info('[bKash] Renewing subscription', [
                    'client_id' => $client->id,
                    'package_id' => $clientPackage->package->id,
                ]);
                $packageService->renewSubscription($client, $clientPackage->package);
            }

            return redirect()->route('client.dashboard')->with('success', 'Payment successful and subscription renewed!');
        }

        Log::error('[bKash Callback] Payment failed', [
            'invoice_id' => $invoice->id,
            'verification' => $verification,
        ]);

        return redirect()->route('client.invoices.index')->with('error', 'Payment not successful: '.($verification['statusMessage'] ?? 'Unknown error'));
    }

    /**
     * Step 3: Get Access Token
     */
    protected function getAccessToken(): ?string
    {
        $config = $this->bkashConfig();

        if (! $this->hasCompleteBkashConfig($config)) {
            Log::error('[bKash] Gateway credentials are not configured.');

            return null;
        }

        try {
            $response = Http::timeout($config['timeout'])
                ->withHeaders([
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'Content-Type' => 'application/json',
                ])->post("{$config['base_url']}/token/grant", [
                    'app_key' => $config['app_key'],
                    'app_secret' => $config['app_secret'],
                ]);

            $data = (array) $response->json();
            Log::info('[bKash] Token Response', $data);

            return filled($data['id_token'] ?? null) ? (string) $data['id_token'] : null;
        } catch (\Exception $e) {
            Log::error('[bKash] Token Error', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Step 4: Create Payment
     */
    protected function createPayment(Invoice $invoice, string $token, int|float|string $amount): array
    {
        $config = $this->bkashConfig();

        try {
            $body = [
                'mode' => '0011',
                'payerReference' => 'invoice_'.$invoice->invoice_number,
                'callbackURL' => route('client.payments.bkash.callback'),
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $invoice->invoice_number,
            ];

            $response = Http::timeout($config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'X-APP-Key' => $config['app_key'],
                    'Content-Type' => 'application/json',
                ])->post("{$config['base_url']}/create", $body);

            $responseData = (array) $response->json();
            Log::info('[bKash] CreatePayment Response', $responseData);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('[bKash] CreatePayment Error', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Step 5: Execute Payment
     */
    protected function executePayment(string $paymentID, string $token): array
    {
        $config = $this->bkashConfig();

        try {
            $response = Http::timeout($config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer '.$token,
                    'X-APP-Key' => $config['app_key'],
                    'Content-Type' => 'application/json',
                ])->post("{$config['base_url']}/execute", [
                    'paymentID' => $paymentID,
                ]);

            $data = (array) $response->json();
            Log::info('[bKash] ExecutePayment Raw Response', $data);

            return $data;
        } catch (\Exception $e) {
            Log::error('[bKash] ExecutePayment Error', [
                'message' => $e->getMessage(),
                'paymentID' => $paymentID,
            ]);

            return [];
        }
    }

    /**
     * Resolve gateway credentials from database settings first, then environment config.
     * The result is cached for the lifetime of the request.
     */
    private function bkashConfig(): array
    {
        if ($this->resolvedBkashConfig !== null) {
            return $this->resolvedBkashConfig;
        }

        $settings = AdminSetting::query()->first();

        return $this->resolvedBkashConfig = [
            'base_url' => rtrim((string) ($settings?->bkash_base_url ?: config('payments.bkash.base_url')), '/'),
            'username' => (string) ($settings?->bkash_username ?: config('payments.bkash.username')),
            'password' => (string) ($settings?->bkash_password ?: config('payments.bkash.password')),
            'app_key' => (string) ($settings?->bkash_app_key ?: config('payments.bkash.app_key')),
            'app_secret' => (string) ($settings?->bkash_app_secret ?: config('payments.bkash.app_secret')),
            'timeout' => max(1, (int) config('payments.bkash.http.timeout', 15)),
        ];
    }

    private function hasCompleteBkashConfig(array $config): bool
    {
        foreach (['base_url', 'username', 'password', 'app_key', 'app_secret'] as $key) {
            if (blank($config[$key] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
