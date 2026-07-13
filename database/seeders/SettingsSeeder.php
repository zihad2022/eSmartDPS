<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // if settings already exists, skip
        if (AdminSetting::count() > 0) {
            return;
        }
        AdminSetting::create([
            /* ========== General Settings ========== */
            'site_name' => 'eSmartDPS',
            'site_slogan' => 'Savings Platform',
            'site_description' => 'eSmartDPS is a savings platform that helps users save money and earn rewards.',
            'site_keywords' => 'savings, platform, money, rewards',
            'meta_codes' => '<meta name="robots" content="index, follow">',
            'site_logo' => 'uploads/logo.png',
            'favicon' => 'uploads/favicon.ico',
            'graph_thumbnail' => 'uploads/graph-thumbnail.png',

            /* ========== Contact Info ========== */
            'helpline_number' => '+8801700000000',
            'email_address' => 'info@esmartdps.com',
            'office_address' => '123, Main Street, Dhaka, Bangladesh',
            'google_map' => '<iframe src="https://maps.google.com/..."></iframe>',

            /* ========== Social Media ========== */
            'facebook_page' => 'https://facebook.com/esmartdps',
            'facebook_group' => 'https://facebook.com/groups/esmartdps',
            'whatsapp_channel' => 'https://wa.me/8801700000000',
            'telegram_channel' => 'https://t.me/esmartdps',
            'linkedin' => 'https://linkedin.com/company/esmartdps',
            'twitter_x' => 'https://twitter.com/esmartdps',
            'youtube' => 'https://youtube.com/@esmartdps',
            'tiktok' => 'https://tiktok.com/@esmartdps',

            /* ========== Payment Settings ========== */
            'currency' => 'BDT',
            'late_fee' => 50.00,

            // bKash
            'bkash_base_url' => env('BKASH_BASE_URL'),
            'bkash_username' => env('BKASH_USERNAME'),
            'bkash_password' => env('BKASH_PASSWORD'),
            'bkash_app_key' => env('BKASH_APP_KEY'),
            'bkash_app_secret' => env('BKASH_APP_SECRET'),
            'bkash_charge'     => 0.00,
            'bkash_status'     => false, // false = disabled, true = active

            // SSLCommerz
            'sslcommerz_store_id' => env('SSLC_STORE_ID'),
            'sslcommerz_store_password' => env('SSLC_STORE_PASSWORD'),
            'sslcommerz_mode' => env('SSLC_SANDBOX', true) ? 'sandbox' : 'live',

            /* ========== SMS Settings ========== */
            'sms_api_key' => env('SMS_API_KEY'),
            'sms_client_id' => env('SMS_CLIENT_ID'),
            'sms_sender_id' => env('SMS_SENDER_ID'),
            'sms_api_url' => env('SMS_API_URL'),
            'sms_balance_api' => env('SMS_BALANCE_API_URL'),
            'sms_message_template' => "Dear {name}, your One-Time Password (OTP) is {otp}. Please use this code to reset your password. This OTP will expire in 5 minutes. - {app_name} Security Team",

            /* ========== Email Settings ========== */
            'mail_host' => env('MAIL_HOST'),
            'mail_port' => env('MAIL_PORT'),
            'mail_username' => env('MAIL_USERNAME'),
            'mail_password' => env('MAIL_PASSWORD'),
            'mail_encryption' => env('MAIL_SCHEME'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS'),
            'mail_from_name' => env('MAIL_FROM_NAME', 'eSmartDPS'),
            'email_message_template' => "Welcome to our platform, {first_name} {last_name}!\n\nYour account has been created successfully.  
Your **User ID** is: {user_id}\nYour temporary password is: {password}\n\nPlease use these credentials to log in to your account.\n\n⚠️ For your security, please log in as soon as possible and change your password immediately.  
Anyone with this password could access your account, so do not share it with anyone.\n\nWe’re excited to have you on board!",

        ]);
    }
}
