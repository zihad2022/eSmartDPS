<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class ContactInfoController extends Controller
{
    public function edit()
    {
        $settings = AdminSetting::first();

        return view('admin.settings.contact-info', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'helpline_number' => 'required',
            'email_address' => 'required',
            'office_address' => 'required',
            'google_map' => 'required',
        ]);

        $settings = AdminSetting::first();
        $settings->update([
            'helpline_number' => $request->helpline_number,
            'email_address' => $request->email_address,
            'office_address' => $request->office_address,
            'google_map' => $request->google_map,
        ]);

        return redirect()->route('admin.settings.contact_info.edit')->with('success', 'Contact info updated successfully.');
    }
}
