<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function create(Invoice $invoice)
    {
        $client = Auth::guard('client')->user();
        $package = $client->clientPackages()->latest()->first()?->package;

        return view('client.checkout', [
            'package' => $package,
            'client' => $client,
            'invoice' => $invoice,
        ]);
    }
}
