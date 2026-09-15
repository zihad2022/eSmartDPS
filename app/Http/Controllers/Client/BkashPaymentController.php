<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Invoice;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

            return redirect()->route('client.invoices.index')->with('info', 'bKash payment was cancelled.');
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

        if ($request->status === 'failure') {
            Log::warning('[bKash Callback] Payment failed from bKash gateway', $request->all());

            return redirect()->route('client.payments.select', $invoice->id)
                ->with('error', 'Payment failed on bKash. Please try again.');
        }

        $token = $this->getAccessToken();
        if (! $token) {
            Log::error('[bKash Callback] Token fetch failed for invoice', [
                'invoice_id' => $invoice->id,
            ]);

            return redirect()->route('client.invoices.index')->with('error', 'bKash gateway authorization failed.');
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
            $packageToActivate = $invoice->package ?? $clientPackage?->package;

            if ($packageToActivate) {
                Log::info('[bKash] Activating package subscription', [
                    'client_id' => $client->id,
                    'package_id' => $packageToActivate->id,
                ]);
                $packageService->renewSubscription($client, $packageToActivate);
            }

            return redirect()->route('client.dashboard')->with('success', 'bKash payment successful! Your subscription is now active.');
        }

        Log::error('[bKash Callback] Payment execution not completed', [
            'invoice_id' => $invoice->id,
            'verification' => $verification,
        ]);

        return redirect()->route('client.invoices.index')->with('error', 'bKash payment not successful: '.($verification['statusMessage'] ?? 'Transaction was not completed.'));
    }

    /**
     * Step 3: Get Access Token
     * Retrieves token from database/cache if still valid (stored for 55 minutes)
     * to avoid unnecessary calls to bKash /token/grant.
     */
    protected function getAccessToken(): ?string
    {
        $config = $this->bkashConfig();

        if (! $this->hasCompleteBkashConfig($config)) {
            Log::error('[bKash] Gateway credentials are not configured.');

            return null;
        }

        // 1. Check database token first
        $settings = AdminSetting::query()->first();
        if ($settings && filled($settings->bkash_id_token) && $settings->bkash_token_expires_at && now()->lt($settings->bkash_token_expires_at)) {
            Log::info('[bKash] Reusing valid cached database token', [
                'expires_at' => $settings->bkash_token_expires_at->toDateTimeString(),
                'remaining_seconds' => now()->diffInSeconds($settings->bkash_token_expires_at),
            ]);

            return (string) $settings->bkash_id_token;
        }

        // 2. Check application cache fallback
        $cachedToken = Cache::get('bkash_access_token');
        if (filled($cachedToken)) {
            Log::info('[bKash] Reusing valid cache fallback token');

            return (string) $cachedToken;
        }

        // 3. Request fresh token from bKash server
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

            $idToken = filled($data['id_token'] ?? null) ? (string) $data['id_token'] : null;

            if ($idToken) {
                $expiresAt = now()->addMinutes(55);

                if ($settings) {
                    $settings->update([
                        'bkash_id_token' => $idToken,
                        'bkash_token_expires_at' => $expiresAt,
                    ]);
                }

                Cache::put('bkash_access_token', $idToken, $expiresAt);

                Log::info('[bKash] Fresh token obtained and cached in database for 55 minutes', [
                    'expires_at' => $expiresAt->toDateTimeString(),
                ]);
            }

            return $idToken;
        } catch (\Exception $e) {
            Log::error('[bKash] Token Error', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Invalidate access token from DB and cache if gateway rejects it
     */
    protected function invalidateAccessToken(): void
    {
        Cache::forget('bkash_access_token');
        $settings = AdminSetting::query()->first();
        if ($settings) {
            $settings->update([
                'bkash_id_token' => null,
                'bkash_token_expires_at' => null,
            ]);
        }
        Log::warning('[bKash] Access token invalidated from database and cache.');
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

            if ($this->isTokenError($responseData)) {
                $this->invalidateAccessToken();
            }

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

            if ($this->isTokenError($data)) {
                $this->invalidateAccessToken();
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('[bKash] ExecutePayment Error', [
                'message' => $e->getMessage(),
                'paymentID' => $paymentID,
            ]);

            return [];
        }
    }

    private function isTokenError(array $data): bool
    {
        $code = (string) ($data['statusCode'] ?? '');
        $msg = (string) ($data['statusMessage'] ?? '');

        return in_array($code, ['2006', '2008', '5001'], true) ||
            stripos($msg, 'token') !== false;
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

        $rawUrl = (string) ($settings?->bkash_base_url ?: config('payments.bkash.base_url'));
        $baseUrl = rtrim($rawUrl, '/');
        // Clean off any trailing specific endpoints if someone entered a full endpoint URL
        $baseUrl = preg_replace('#/(create|execute|token/grant|payment/status)$#i', '', $baseUrl);

        return $this->resolvedBkashConfig = [
            'base_url' => $baseUrl,
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
