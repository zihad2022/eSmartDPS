<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Show the Payment Settings edit page.
     *
     * Fetches the first AdminSetting record and passes it to the view.
     * Assumes only one settings row exists.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // Retrieve the admin settings record (single row)
        $settings = AdminSetting::first();

        // Return the payment settings view with current settings
        return view('admin.settings.payment', compact('settings'));
    }

    /**
     * Update payment settings based on submitted form data.
     *
     * Handles multiple payment setting sections:
     * - general (currency, late fee)
     * - bkash (bkash credentials)
     * - uddoktapay (API credentials and callback URL)
     * - sslcommerz (store ID, password, mode)
     *
     * Validates input per section, updates the settings,
     * then redirects back with success flash message.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Determine which payment section is being updated
        $section = $request->input('section');

        // Retrieve current settings
        $settings = AdminSetting::first();

        // Validate and update General Payment Settings
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

        // Validate and update bKash Settings
        if ($section === 'bkash') {
            $request->validate([
                'bkash_base_url'   => 'required|url',
                'bkash_username'   => 'required|string',
                'bkash_password'   => 'required|string',
                'bkash_app_key'    => 'required|string',
                'bkash_app_secret' => 'required|string',
                'bkash_charge'     => 'required|numeric|min:0',
                'bkash_status'     => 'required|boolean',
            ]);
        
            $settings->update([
                'bkash_base_url'   => $request->bkash_base_url,
                'bkash_username'   => $request->bkash_username,
                'bkash_password'   => $request->bkash_password,
                'bkash_app_key'    => $request->bkash_app_key,
                'bkash_app_secret' => $request->bkash_app_secret,
                'bkash_charge'     => $request->bkash_charge,
                'bkash_status'     => $request->bkash_status,
            ]);
        
            return back()->with('success', 'bKash settings updated successfully!');
        }
        

        // Validate and update UddoktaPay Settings
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

        // Validate and update SSLCommerz Settings
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

        // Log activity
        ActivityLogger::log('Payment Settings Updated');

        // Fallback: in case no section matched, return with generic success message
        return back()->with('success', 'Settings updated successfully!');
    }
}
