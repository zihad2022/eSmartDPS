<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SubscriptionPaymentController extends Controller
{
    /**
     * Process the selected payment method.
     */
    public function processPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'payment_method' => 'required|in:bkash,sslcommerz',
        ]);

        if ($invoice->status !== InvoiceStatus::UNPAID) {
            return redirect()->route('client.invoices.index')
                ->with('info', 'This invoice is already paid.');
        }

        switch ($request->payment_method) {
            case 'bkash':
                // Directly trigger bKash payment logic
                return app(BkashPaymentController::class)->pay($invoice);
            case 'sslcommerz':
                // Directly trigger SSLCommerz payment logic
                return app(SslcommerzPaymentController::class)->pay($invoice);
        }

        return back()->with('error', 'Invalid payment method selected.');
    }

}
