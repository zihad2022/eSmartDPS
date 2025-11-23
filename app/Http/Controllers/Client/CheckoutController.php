<?php

namespace App\Http\Controllers\Client;

use App\Domain\Invoices\Models\Invoice;
use App\Domain\Packages\Models\Package;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
