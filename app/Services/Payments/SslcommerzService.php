<?php

namespace App\Services\Payments;

use App\Domain\Invoices\Invoice;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SslcommerzService
{
    protected string $storeId;
    protected string $storePassword;
    protected bool $sandbox;
    protected int $timeout;

    public function __construct()
    {
        // Defaults from config/env
        $storeId       = config('payments.sslcommerz.store_id');
        $storePassword = config('payments.sslcommerz.store_password');
        $sandbox       = (bool) config('payments.sslcommerz.sandbox', true);
        $timeout       = (int) (config('payments.sslcommerz.http.timeout', 10));

        // Override from AdminSetting if present (cached to avoid DB hit every request)
        $settings = Cache::remember('admin_settings_first', 60, function () {
            return AdminSetting::first();
        });
        if ($settings) {
            if (! empty($settings->sslcommerz_store_id)) {
                $storeId = $settings->sslcommerz_store_id;
            }
            if (! empty($settings->sslcommerz_store_password)) {
                $storePassword = $settings->sslcommerz_store_password;
            }
            if (! empty($settings->sslcommerz_mode)) {
                // live => false sandbox; sandbox => true sandbox
                $sandbox = strtolower($settings->sslcommerz_mode) !== 'live';
            }
        }

        $this->storeId       = $storeId;
        $this->storePassword = $storePassword;
        $this->sandbox       = $sandbox;
        $this->timeout       = $timeout;
    }

    /**
     * Initialize payment and return Gateway URL.
     *
     * @return array{ok:bool,url?:string,error?:string,request?:array,response?:array}
     */
    public function initiate(Invoice $invoice): array
    {
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

        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post($url, $postData)
            ->json();

        if (! $response || empty($response['GatewayPageURL'])) {
            Log::error('SSLCommerz initialization failed', [
                'response' => $response,
                'request'  => $postData,
            ]);

            return [
                'ok' => false,
                'error' => 'Unable to connect to SSLCommerz gateway.',
                'request' => $postData,
                'response' => $response,
            ];
        }

        return [
            'ok'  => true,
            'url' => $response['GatewayPageURL'],
            'request' => $postData,
            'response' => $response,
        ];
    }

    /**
     * Verify transaction with the Validator API.
     */
    public function validate(array $params): bool
    {
        $url = $this->sandbox
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php';

        $response = Http::timeout($this->timeout)->get($url, [
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
