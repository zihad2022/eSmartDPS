<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

class InvoiceSendToClientController extends Controller
{
    /**
     * Send an invoice to the client's email.
     */
    public function __invoke(Invoice $invoice): RedirectResponse
    {
        // -----------------------------
        // 1. Fetch client email
        // -----------------------------
        $clientEmail = $invoice->client->email;

        // -----------------------------
        // 2. Check if client has email
        // -----------------------------
        if (!$clientEmail) {
            return redirect()->back()->with('error', 'Client does not have an email address.');
        }

        // -----------------------------
        // 3. Send invoice email (queued for better performance)
        // -----------------------------
        Mail::to($clientEmail)->send(new InvoiceMail($invoice));

        // -----------------------------
        // 4. Redirect back with success
        // -----------------------------
        return redirect()->back()->with('success', 'Invoice has been sent to the client successfully.');
    }
}
