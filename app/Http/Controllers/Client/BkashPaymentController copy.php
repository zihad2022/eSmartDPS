<?php

namespace App\Http\Controllers\Client;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BkashPaymentController extends Controller
{
    // Static sandbox credentials
    protected $baseUrl = "https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout";
    protected $username = "sandboxTokenizedUser02";
    protected $password = "sandboxTokenizedUser02@12345";
    protected $appKey = "4f6o0cjiki2rfm34kfdadl1eqq";
    protected $appSecret = "2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b";

    /**
     * Step 1: Start payment (redirect to bKash checkout)
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

        $paymentResponse = $this->createPayment($invoice, $token, $amount);

        if (!isset($paymentResponse['paymentID']) || !isset($paymentResponse['bkashURL'])) {
            Log::error("bKash: No paymentID or bkashURL for Invoice {$invoice->invoice_number}", $paymentResponse);
            return back()->with('error', 'Unable to initiate bKash payment: ' . ($paymentResponse['statusMessage'] ?? 'Unknown error'));
        }

        $invoice->update([
            'payment_reference' => $paymentResponse['paymentID'],
        ]);

        // Redirect to bKash checkout
        return redirect($paymentResponse['bkashURL']);
    }

    /**
     * Step 2: Callback after payment approval
     */
    public function callback(Request $request, PackageService $packageService)
    {
        Log::info('bKash Callback Data:', $request->all());

        if ($request->status === 'cancel' || $request->has('cancel')) {
            return redirect()->route('client.invoices.index')->with('info', 'Payment was cancelled.');
        }

        $paymentID = $request->paymentID ?? $request->trxID;
        if (!$paymentID) {
            return redirect()->route('client.invoices.index')->with('error', 'Missing payment reference.');
        }

        $invoice = Invoice::where('payment_reference', $paymentID)->first();
        if (!$invoice) {
            return redirect()->route('client.invoices.index')->with('error', 'Invalid payment reference.');
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return redirect()->route('client.invoices.index')->with('error', 'Payment verification failed.');
        }

        $verification = $this->executePayment($paymentID, $token);
        Log::info("bKash Execute Payment Response", $verification);

        if (($verification['statusCode'] ?? null) === '0000' && ($verification['transactionStatus'] ?? null) === 'Completed') {
            $invoice->update([
                'status' => \App\Enums\InvoiceStatus::PAID,
                'paid_at' => now(),
                'payment_method' => PaymentMethod::ONLINE,
                'trx_id' => $verification['trxID'] ?? $paymentID,
            ]);

            $client = $invoice->client;
            $clientPackage = $client->activeClientPackage ?? $client->latestClientPackage;
            if ($clientPackage && $clientPackage->package) {
                $packageService->renewSubscription($client, $clientPackage->package);
            }

            return redirect()->route('client.dashboard')->with('success', 'Payment successful and subscription renewed!');
        }

        return redirect()->route('client.invoices.index')->with('error', 'Payment not successful: ' . ($verification['statusMessage'] ?? 'Unknown error'));
    }

    /**
     * Step 3: Get Access Token
     */
    protected function getAccessToken()
    {
        try {
            $response = Http::withHeaders([
                'username' => $this->username,
                'password' => $this->password,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/token/grant", [
                'app_key'    => $this->appKey,
                'app_secret' => $this->appSecret,
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
     * Step 4: Create Payment
     */
    protected function createPayment(Invoice $invoice, $token, $amount)
    {
        try {
            $body = [
                'mode'                  => '0011',
                'payerReference'        => 'invoice_' . $invoice->invoice_number,
                'callbackURL'           => route('client.payments.bkash.callback'),
                'amount'                => number_format($amount, 2, '.', ''),
                'currency'              => 'BDT',
                'intent'                => 'sale',
                'merchantInvoiceNumber' => $invoice->invoice_number,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-APP-Key'     => $this->appKey,
                'Content-Type'  => 'application/json',
            ])->post("{$this->baseUrl}/create", $body);

            $responseData = $response->json();
            Log::info("bKash Create Payment Response", $responseData);

            return $responseData;
        } catch (\Exception $e) {
            Log::error("bKash Create Payment Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Step 5: Execute Payment
     */
    protected function executePayment($paymentID, $token)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-APP-Key'     => $this->appKey,
                'Content-Type'  => 'application/json',
            ])->post("{$this->baseUrl}/execute", [
                'paymentID' => $paymentID,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("bKash Execute Payment Error: " . $e->getMessage());
            return [];
        }
    }
}
