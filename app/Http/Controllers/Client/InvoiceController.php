<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $clientId = owner_client_id();
        $client = Client::find($clientId);
        $invoices = $client->invoices()->latest()->paginate(10);  
        
        $totalInvoices = $client->invoices()->count();
        $totalPaidInvoices = $client->invoices()->where('status', InvoiceStatus::PAID)->count();
        $totalUnpaidInvoices = $client->invoices()->where('status', InvoiceStatus::UNPAID)->count();
     return view('client.invoice.index', compact('invoices', 'totalInvoices', 'totalPaidInvoices', 'totalUnpaidInvoices'));   
    }
}
