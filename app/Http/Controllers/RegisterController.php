<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\ClientSetting;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Services\PackageService;
use App\Services\MailService;

class RegisterController extends Controller
{
    protected $packageService;
    protected $mailService;

    public function __construct(PackageService $packageService, MailService $mailService)
    {
        $this->packageService = $packageService;
        $this->mailService = $mailService;
    }

    public function create()
    {
        $package = Package::find(request('package'));
        $settings = AdminSetting::first();
        return view('auth.register', compact('package', 'settings'));
    }

    public function store(Request $request)
    {
        $client = new Client();
        $client->user_id = generate_client_user_id();
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->password = $request->password;
        $client->status = true;
        $client->role = 'admin';
        $client->save();

        $package = Package::findOrFail($request->package_id);
        $this->packageService->startPackage($client, $package);

        $clientSetting = new ClientSetting();
        $clientSetting->client_id = $client->id;
        $clientSetting->organization_name = $request->organization_name;
        $clientSetting->short_name = $request->short_name;
        $clientSetting->contact_email = $request->contact_email;
        $clientSetting->contact_phone = $request->contact_phone;
        $clientSetting->currency = 'BDT';
        $clientSetting->save();
        // Send credentials to the client via email
        $this->mailService->sendMail($client, $request['password']);
        return redirect()->route('auth.success', ['id' => $package->id]);
    }

    public function success($id)
    {
        $package = Package::findOrFail($id);
        return view('auth.success', compact('package'));
    }
}
