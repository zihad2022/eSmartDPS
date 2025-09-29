<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Payments\PaymentService;
use App\Services\Payments\Gateways\BkashGateway;
use Illuminate\Http\Request;

class BkashPaymentController extends Controller
{
    public function pay(Invoice $invoice)
    {
        $paymentService = new PaymentService(new BkashGateway());

        $paymentResponse = $paymentService->initiate($invoice);

        if (!isset($paymentResponse['paymentID'], $paymentResponse['bkashURL'])) {
            return back()->with('error', 'Unable to initiate payment');
        }

        $invoice->update([
            'payment_reference' => $paymentResponse['paymentID'],
        ]);

        return redirect($paymentResponse['bkashURL']);
    }

    public function callback(Request $request, Invoice $invoice)
    {
        $paymentService = new PaymentService(new BkashGateway());

        $success = $paymentService->verifyAndProcess($invoice, $request->paymentID ?? $request->trxID);

        return $success
            ? redirect()->route('client.dashboard')->with('success', 'Payment successful!')
            : redirect()->route('client.invoices.index')->with('error', 'Payment failed');
    }
}
