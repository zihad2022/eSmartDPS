<?php

namespace App\Http\Controllers\Client\Auth;

use App\Domain\Billing\Models\Invoice;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSetting;
use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Domain\Clients\Services\PackageService;
use App\Enums\InvoiceStatus;
use App\Services\MailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    protected PackageService $packageService;
    protected MailService $mailService;

    /**
     * Inject required services.
     */
    public function __construct(PackageService $packageService, MailService $mailService)
    {
        $this->packageService = $packageService;
        $this->mailService = $mailService;
    }

    /**
     * Show registration form for a client with selected package.
     */
    public function create(): View
    {
        // -----------------------------
        // 1. Get package and admin settings
        // -----------------------------
        $package = Package::find(request('package'));
        $settings = AdminSetting::first();

        // -----------------------------
        // 2. Return registration view
        // -----------------------------
        return view('client.auth.register', compact('package', 'settings'));
    }

    /**
     * Store a new client and start the selected package.
     */
    public function store(Request $request): RedirectResponse
    {
        // -----------------------------
        // 1. Create client
        // -----------------------------
        $client = Client::create([
            'user_id'    => generate_client_user_id(),
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => $request->password, // TODO: Hash this later
            'status'     => true,
            'role'       => 'super_admin',
        ]);

        // -----------------------------
        // 2. Start selected package
        // -----------------------------
        $package = Package::findOrFail($request->package_id);
        $this->packageService->startPackage($client, $package);

        // -----------------------------
        // 3. Update client settings
        // -----------------------------
        $client->settings()->update([
            'organization_name' => $request->organization_name,
            'short_name'        => $request->short_name,
            'contact_email'     => $request->contact_email,
            'contact_phone'     => $request->contact_phone,
            'currency'          => 'BDT',
        ]);


        // =====================================================
        // 4. Generate Invoice (Directly Inside Controller)
        // =====================================================

        $billingStart = now()->toDateString();
        $billingEnd   = now()->addMonth()->toDateString();       // 1 Month package cycle
        $nextInvoice  = now()->addMonth()->toDateTimeString();   // Next invoice date

        $invoiceNumber = generate_invoice_number();          // Unique invoice ID

        $invoice = Invoice::create([
            'client_id'           => $client->id,
            'package_id'          => $package->id,

            // Snapshot
            'package_name'        => $package->name,
            'package_description' => $package->description,

            // Billing
            'billing_start'       => $billingStart,
            'billing_end'         => $billingEnd,

            // Invoice info
            'invoice_number'      => $invoiceNumber,
            'invoice_amount'      => $package->price,
            'status'              => InvoiceStatus::UNPAID,  
            'paid_at'             => null,
            'next_invoice_at'     => $nextInvoice,

            // Payment info (null for now)
            'payment_reference'   => null,
            'payment_id'          => null,
            'trx_id'              => null,
            'payment_method'      => null,
            'wallet_address'      => null,
        ]);


        // -----------------------------
        // 5. Send credentials via email
        // -----------------------------
        $this->mailService->sendMail($client, $request->password);

        // -----------------------------
        // 6. Redirect to success page
        // -----------------------------
        return redirect()->route('client.auth.success', [
            'id'       => $package->id,
            'invoice'  => $invoice->id,
        ]);
    }


    /**
     * Show success page after registration.
     */
    public function success(int $id): View
    {
        // -----------------------------
        // 1. Fetch package info
        // -----------------------------
        $package = Package::findOrFail($id);

        // -----------------------------
        // 2. Return success view
        // -----------------------------
        return view('client.auth.success', compact('package'));
    }
}
