<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InvoiceSendToClientController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Invoice $invoice)
    {
        $clientEmail = $invoice->client->email;

        if (!$clientEmail) {
            return redirect()->back()->with('error', 'Client does not have an email.');
        }
    
        // Send invoice email
        Mail::to($clientEmail)->send(new InvoiceMail($invoice));
    
        return redirect()->back()->with('success', 'Invoice sent to client successfully.');
    }
}
