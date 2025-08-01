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
            'bkash_app_key' => 'bkash_test_app_key',
            'bkash_app_secret' => 'bkash_test_secret',
            'bkash_username' => 'bkash_test_user',
            'bkash_password' => 'bkash_test_pass',

            // UddoktaPay
            'uddoktapay_api_key' => 'uddokta_test_api_key',
            'uddoktapay_secret' => 'uddokta_test_secret',
            'uddoktapay_callback_url' => 'https://esmartdps.com/callback/uddoktapay',

            // SSLCommerz
            'sslcommerz_store_id' => 'test_store_id',
            'sslcommerz_store_password' => 'test_store_pass',
            'sslcommerz_mode' => 'sandbox',

            /* ========== SMS Settings ========== */
            'sms_api_key' => 'sms_test_api_key',
            'sms_secret_key' => 'sms_test_secret',
            'sms_sender_id' => 'MYBIZ',
            'sms_api_url' => 'https://smsprovider.com/api/send',
            'sms_balance_api' => 'https://smsprovider.com/api/balance',
            'sms_message_template' => 'Hello {name}, your OTP is {otp}.',

            /* ========== Email Settings ========== */
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => '465',
            'mail_username' => 'zihadulislamafnan@gmail.com',
            'mail_password' => 'cmxafjhcpnybjlqq',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'zihadulislamafnan@gmail.com',
            'mail_from_name' => 'eSmartDPS',
        ]);
    }
}
