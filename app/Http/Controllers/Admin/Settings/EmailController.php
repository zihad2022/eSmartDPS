<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function edit()
    {
        $settings = AdminSetting::first();

        return view('admin.settings.email', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mail_host' => 'required',
            'mail_port' => 'required',
            'mail_username' => 'required',
            'mail_password' => 'required',
            'mail_encryption' => 'required',
            'mail_from_address' => 'required',
            'mail_from_name' => 'required',
        ]);

        $settings = AdminSetting::first();
        $settings->update([
            'mail_host' => $request->mail_host,
            'mail_port' => $request->mail_port,
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,
            'mail_encryption' => $request->mail_encryption,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,
        ]);

        return redirect()->route('admin.settings.email.edit')->with('success', 'Email settings updated successfully.');
    }
}
