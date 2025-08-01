<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;

class SmsController extends Controller
{
    public function edit()
    {
        $settings = AdminSetting::first();

        return view('admin.settings.sms', compact('settings'));
    }
}
