<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Domain\Clients\Models\Client;
use App\Domain\Packages\Models\Package;
use App\Domain\Clients\Services\PackageService;
use App\Services\MailService;
use Illuminate\Http\Request;
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
        return view('auth.register', compact('package', 'settings'));
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
            'password'   => $request->password, // TODO: consider hashing
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

        // -----------------------------
        // 4. Send credentials via email
        // -----------------------------
        $this->mailService->sendMail($client, $request->password);

        // -----------------------------
        // 5. Redirect to success page
        // -----------------------------
        return redirect()->route('auth.success', ['id' => $package->id]);
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
        return view('auth.success', compact('package'));
    }
}
