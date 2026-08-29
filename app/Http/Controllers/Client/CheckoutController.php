<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Invoice $invoice): View
    {
        $client = Auth::guard('client')->user();
        $package = $client->clientPackages()->with('package')->latest()->first()?->package;

        return view('client.checkout', [
            'package' => $package,
            'client' => $client,
            'invoice' => $invoice,
        ]);
    }
}
