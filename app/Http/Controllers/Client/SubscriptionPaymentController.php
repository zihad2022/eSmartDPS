<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Enums\InvoiceStatus;
use App\Http\Controllers\SslCommerzController;
use App\Http\Controllers\UddoktapayController;
use App\Models\AdminSetting;

class SubscriptionPaymentController extends Controller
{
    /**
     * Show payment method selection for existing invoice.
     */
    public function selectMethod(Invoice $invoice)
    {
        if ($invoice->status !== InvoiceStatus::UNPAID) {
            return redirect()->route('client.invoices.index')
                ->with('info', 'This invoice is already paid.');
        }

        $paymentMethods = $this->availableMethods();

        $settings = AdminSetting::first();
        return view('client.payments.select-method', compact('invoice', 'paymentMethods', 'settings'));
    }

    /**
     * Start package subscription → generate invoice first, then go to select method.
     */
    public function startPackage(Package $package, Request $request)
    {
        // Create invoice for this package if not exists
        $invoice = Invoice::create([
            'client_id'   => auth('client')->id(),
            'package_id'  => $package->id,
            'amount'      => $package->price,
            'status'      => InvoiceStatus::UNPAID,
            'due_date'    => now()->addDays(7),
        ]);

        return redirect()->route('client.payments.select', $invoice->id);
    }

    /**
     * Process the selected payment method.
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_method' => 'required|in:bkash,uddoktapay,sslcommerz',
        ]);
    
        if ($invoice->status !== \App\Enums\InvoiceStatus::UNPAID) {
            return redirect()->route('client.invoices.index')
                ->with('info', 'This invoice is already paid.');
        }
    
        switch ($request->payment_method) {
            case 'bkash':
                // Directly trigger bKash payment logic
                return app(BkashPaymentController::class)->pay($invoice);
    
            case 'uddoktapay':
                // Call your Uddoktapay payment method directly
                return app(UddoktapayController::class)->pay($invoice);
    
            case 'sslcommerz':
                // Call your SSLCommerz payment method directly
                return app(SslCommerzController::class)->pay($invoice);
        }
    
        return back()->with('error', 'Invalid payment method selected.');
    }
    

    /**
     * Helper: available payment methods
     */
    private function availableMethods()
    {
        return [
            'bkash'      => 'bKash',
            'uddoktapay' => 'Uddoktapay',
            'sslcommerz' => 'SSLCommerz',
        ];
    }
}
