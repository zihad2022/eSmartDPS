<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;

class InvoiceSendToClientController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Invoice $invoice)
    {
        // -----------------------------
        // 1. Fetch client email
        // -----------------------------
        $clientEmail = $invoice->client->email;

        // -----------------------------
        // 2. Check if client has email
        // -----------------------------
        if (!$clientEmail) {
            return redirect()->back()->with('error', 'Client does not have an email.');
        }

        // -----------------------------
        // 3. Send invoice email
        // -----------------------------
        Mail::to($clientEmail)->send(new InvoiceMail($invoice));

        // -----------------------------
        // 4. Redirect with success message
        // -----------------------------
        return redirect()->back()->with('success', 'Invoice sent to client successfully.');
    }
}
