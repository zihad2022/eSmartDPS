<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function edit()
    {
        $settings = AdminSetting::first();

        return view('admin.settings.payment', compact('settings'));
    }

    public function update(Request $request)
    {
        $section = $request->input('section');
        $settings = AdminSetting::first();
        if ($section === 'general') {
            $request->validate([
                'currency' => 'required|string|max:10',
                'late_fee' => 'nullable|numeric|min:0',
            ]);
            $settings->update([
                'currency' => $request->currency,
                'late_fee' => $request->late_fee,
            ]);

            return back()->with('success', 'General Settings updated successfully!');
        }

        if ($section === 'bkash') {
            $request->validate([
                'bkash_app_key' => 'required|string',
                'bkash_app_secret' => 'required|string',
                'bkash_username' => 'required|string',
                'bkash_password' => 'required|string',
            ]);
            $settings->update([
                'bkash_app_key' => $request->bkash_app_key,
                'bkash_app_secret' => $request->bkash_app_secret,
                'bkash_username' => $request->bkash_username,
                'bkash_password' => $request->bkash_password,
            ]);

            return back()->with('success', 'bKash Settings updated successfully!');
        }

        if ($section === 'uddoktapay') {
            $request->validate([
                'uddoktapay_api_key' => 'required|string',
                'uddoktapay_secret' => 'required|string',
                'uddoktapay_callback_url' => 'nullable|url',
            ]);
            $settings->update([
                'uddoktapay_api_key' => $request->uddoktapay_api_key,
                'uddoktapay_secret' => $request->uddoktapay_secret,
                'uddoktapay_callback_url' => $request->uddoktapay_callback_url,
            ]);

            return back()->with('success', 'UddoktaPay Settings updated successfully!');
        }

        if ($section === 'sslcommerz') {
            $request->validate([
                'sslcommerz_store_id' => 'required|string',
                'sslcommerz_store_password' => 'required|string',
                'sslcommerz_mode' => 'required|in:live,sandbox',
            ]);
            $settings->update([
                'sslcommerz_store_id' => $request->sslcommerz_store_id,
                'sslcommerz_store_password' => $request->sslcommerz_store_password,
                'sslcommerz_mode' => $request->sslcommerz_mode,
            ]);

            return back()->with('success', 'SSLCommerz Settings updated successfully!');
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
