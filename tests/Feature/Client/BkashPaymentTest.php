<?php

namespace Tests\Feature\Client;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BkashPaymentTest extends TestCase
{
    use RefreshDatabase;

    private Client $client;
    private Package $package;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        AdminSetting::query()->firstOrCreate([], [
            'site_name' => 'eSmartDPS',
            'currency' => 'BDT',
            'bkash_base_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout',
            'bkash_username' => 'sandboxTokenizedUser02',
            'bkash_password' => 'sandboxTokenizedUser02@12345',
            'bkash_app_key' => '4f6o0cjiki2rfm34kfdadl1eqq',
            'bkash_app_secret' => '2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b',
            'bkash_status' => true,
        ]);

        $this->package = Package::factory()->create([
            'name' => 'Standard Growth Plan',
            'price' => 1500,
            'billing_cycle' => \App\Enums\Package\BillingCycle::MONTHLY,
            'is_active' => true,
        ]);

        $this->client = Client::factory()->create([
            'status' => true,
        ]);

        $this->invoice = Invoice::create([
            'client_id' => $this->client->id,
            'package_id' => $this->package->id,
            'package_name' => $this->package->name,
            'package_description' => $this->package->description,
            'billing_start' => now()->startOfDay(),
            'billing_end' => now()->addMonth()->endOfDay(),
            'due_date' => now()->addDays(7),
            'invoice_number' => 'INV-BKASH-TEST-001',
            'invoice_amount' => 1500,
            'status' => InvoiceStatus::UNPAID,
        ]);
    }

    public function test_client_can_view_packages_and_initiate_paid_subscription(): void
    {
        $response = $this->actingAs($this->client, 'client')
            ->get(route('client.subscription.start.paid', $this->package));

        $response->assertRedirect(route('client.payments.select', $this->invoice->id));
    }

    public function test_client_can_view_payment_method_selection_with_bkash(): void
    {
        $response = $this->actingAs($this->client, 'client')
            ->get(route('client.payments.select', $this->invoice->id));

        $response->assertOk()
            ->assertSee('bKash Direct Checkout')
            ->assertSee('1,500');
    }

    public function test_client_initiating_bkash_payment_redirects_to_bkash_gateway(): void
    {
        Http::fake([
            '*/token/grant' => Http::response([
                'statusCode' => '0000',
                'id_token' => 'mocked-jwt-token',
            ]),
            '*/create' => Http::response([
                'statusCode' => '0000',
                'paymentID' => 'TR0011TESTPAYMENTID',
                'bkashURL' => 'https://sandbox.payment.bkash.com/mock-checkout',
            ]),
        ]);

        $response = $this->actingAs($this->client, 'client')
            ->post(route('client.payments.process', $this->invoice->id), [
                'payment_method' => 'bkash',
            ]);

        $response->assertRedirect('https://sandbox.payment.bkash.com/mock-checkout');

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'payment_reference' => 'TR0011TESTPAYMENTID',
        ]);
    }

    public function test_bkash_callback_handles_user_cancellation(): void
    {
        $this->invoice->update(['payment_reference' => 'TR0011CANCELLED']);

        $response = $this->actingAs($this->client, 'client')
            ->get(route('client.payments.bkash.callback', [
                'paymentID' => 'TR0011CANCELLED',
                'status' => 'cancel',
            ]));

        $response->assertRedirect(route('client.invoices.index'))
            ->assertSessionHas('info');

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => InvoiceStatus::UNPAID,
        ]);
    }

    public function test_bkash_callback_handles_payment_failure(): void
    {
        $this->invoice->update(['payment_reference' => 'TR0011FAILED']);

        $response = $this->actingAs($this->client, 'client')
            ->get(route('client.payments.bkash.callback', [
                'paymentID' => 'TR0011FAILED',
                'status' => 'failure',
            ]));

        $response->assertRedirect(route('client.payments.select', $this->invoice->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('invoices', [
            'id' => $this->invoice->id,
            'status' => InvoiceStatus::UNPAID,
        ]);
    }

    public function test_bkash_callback_successful_payment_marks_invoice_paid_and_activates_package(): void
    {
        $this->invoice->update(['payment_reference' => 'TR0011SUCCESS']);

        Http::fake([
            '*/token/grant' => Http::response([
                'statusCode' => '0000',
                'id_token' => 'mocked-jwt-token',
            ]),
            '*/execute' => Http::response([
                'statusCode' => '0000',
                'transactionStatus' => 'Completed',
                'trxID' => 'TRX99887766',
            ]),
        ]);

        $response = $this->actingAs($this->client, 'client')
            ->get(route('client.payments.bkash.callback', [
                'paymentID' => 'TR0011SUCCESS',
                'status' => 'success',
            ]));

        $response->assertRedirect(route('client.dashboard'))
            ->assertSessionHas('success');

        $this->invoice->refresh();

        $this->assertTrue($this->invoice->isPaid());
        $this->assertEquals(PaymentMethod::ONLINE, $this->invoice->payment_method);
        $this->assertEquals('TRX99887766', $this->invoice->trx_id);

        // Assert subscription package is activated for the client
        $this->assertDatabaseHas('client_packages', [
            'client_id' => $this->client->id,
            'package_id' => $this->package->id,
            'is_active' => true,
        ]);
    }

    public function test_bkash_token_is_stored_in_database_for_55_minutes_and_reused(): void
    {
        $grantCalls = 0;

        Http::fake([
            '*/token/grant' => function () use (&$grantCalls) {
                $grantCalls++;

                return Http::response([
                    'statusCode' => '0000',
                    'id_token' => 'jwt-token-cached-55-minutes',
                ]);
            },
            '*/create' => Http::response([
                'statusCode' => '0000',
                'paymentID' => 'TR0011TESTPAYMENTID',
                'bkashURL' => 'https://sandbox.payment.bkash.com/mock-checkout',
            ]),
        ]);

        // First payment call - should fetch from bKash and store in DB
        $this->actingAs($this->client, 'client')
            ->post(route('client.payments.process', $this->invoice->id), [
                'payment_method' => 'bkash',
            ]);

        $this->assertEquals(1, $grantCalls, 'Token grant must be called once initially');

        $settings = AdminSetting::query()->first();
        $this->assertEquals('jwt-token-cached-55-minutes', $settings->bkash_id_token);
        $this->assertNotNull($settings->bkash_token_expires_at);
        $this->assertTrue($settings->bkash_token_expires_at->gt(now()->addMinutes(50)));
        $this->assertTrue($settings->bkash_token_expires_at->lte(now()->addMinutes(56)));

        // Create a second invoice for the client with different billing dates
        $invoice2 = Invoice::create([
            'client_id' => $this->client->id,
            'package_id' => $this->package->id,
            'package_name' => $this->package->name,
            'package_description' => $this->package->description,
            'billing_start' => now()->addMonth()->startOfDay(),
            'billing_end' => now()->addMonths(2)->endOfDay(),
            'due_date' => now()->addDays(14),
            'invoice_number' => 'INV-BKASH-TEST-002',
            'invoice_amount' => 1500,
            'status' => InvoiceStatus::UNPAID,
        ]);

        // Second payment call - should reuse the stored database token without calling /token/grant
        $this->actingAs($this->client, 'client')
            ->post(route('client.payments.process', $invoice2->id), [
                'payment_method' => 'bkash',
            ]);

        $this->assertEquals(1, $grantCalls, 'Token grant must NOT be called again within 55 minutes window');

        // Now fast forward / expire token in database
        $settings->update([
            'bkash_token_expires_at' => now()->subMinute(),
        ]);
        \Illuminate\Support\Facades\Cache::forget('bkash_access_token');

        // Third invoice - expired token should trigger new /token/grant call
        $invoice3 = Invoice::create([
            'client_id' => $this->client->id,
            'package_id' => $this->package->id,
            'package_name' => $this->package->name,
            'package_description' => $this->package->description,
            'billing_start' => now()->addMonths(2)->startOfDay(),
            'billing_end' => now()->addMonths(3)->endOfDay(),
            'due_date' => now()->addDays(21),
            'invoice_number' => 'INV-BKASH-TEST-003',
            'invoice_amount' => 1500,
            'status' => InvoiceStatus::UNPAID,
        ]);

        $this->actingAs($this->client, 'client')
            ->post(route('client.payments.process', $invoice3->id), [
                'payment_method' => 'bkash',
            ]);

        $this->assertEquals(2, $grantCalls, 'Token grant must be called again once token has expired');
    }

    public function test_different_clients_share_same_database_token_within_55_minutes(): void
    {
        $grantCalls = 0;

        Http::fake([
            '*/token/grant' => function () use (&$grantCalls) {
                $grantCalls++;

                return Http::response([
                    'statusCode' => '0000',
                    'id_token' => 'shared-client-db-token',
                ]);
            },
            '*/create' => Http::response([
                'statusCode' => '0000',
                'paymentID' => 'TR0011TESTPAYMENTID',
                'bkashURL' => 'https://sandbox.payment.bkash.com/mock-checkout',
            ]),
        ]);

        // Client 1 pays
        $this->actingAs($this->client, 'client')
            ->post(route('client.payments.process', $this->invoice->id), [
                'payment_method' => 'bkash',
            ]);

        $this->assertEquals(1, $grantCalls, 'Client 1 causes initial token grant');

        // Client 2 with their own invoice
        $client2 = Client::factory()->create(['status' => true]);
        $invoiceClient2 = Invoice::create([
            'client_id' => $client2->id,
            'package_id' => $this->package->id,
            'package_name' => $this->package->name,
            'package_description' => $this->package->description,
            'billing_start' => now()->startOfDay(),
            'billing_end' => now()->addMonth()->endOfDay(),
            'due_date' => now()->addDays(7),
            'invoice_number' => 'INV-CLIENT2-001',
            'invoice_amount' => 1500,
            'status' => InvoiceStatus::UNPAID,
        ]);

        // Client 2 pays in a new session/request - token must be reused from DB
        $this->actingAs($client2, 'client')
            ->post(route('client.payments.process', $invoiceClient2->id), [
                'payment_method' => 'bkash',
            ]);

        $this->assertEquals(1, $grantCalls, 'Client 2 reuses the database token without calling bKash server');
    }
}
