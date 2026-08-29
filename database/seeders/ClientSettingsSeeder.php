<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::whereNull('parent_id')->with('settings')->get();

        foreach ($clients as $client) {
            $settings = $client->settings;

            if (! $settings) {
                continue; // Skip if somehow no settings exist
            }

            $settings->update([
                // General Settings
                'organization_name' => $client->first_name.' '.$client->last_name,
                'short_name' => strtoupper(substr($client->first_name, 0, 3)),
                'contact_email' => $client->email ?? 'contact@example.com',
                'contact_phone' => '017'.rand(10000000, 99999999),
                'address' => '123 Demo Street, Dhaka, Bangladesh',
                'currency' => 'BDT',

                // Share Settings
                'share_price' => rand(100, 1000),
                'minimum_shares' => 1,
                'maximum_shares' => 100,
                'share_transfer_fee' => rand(10, 50),
                'allow_partial_shares' => (bool) rand(0, 1),

                // Payment Settings
                'payment_due_date' => collect(['1', '15', '30'])->random(),
                'late_payment_fee' => rand(50, 200),
                'grace_period_days' => rand(3, 10),
                'payment_methods' => [1, 2, 3], // will auto-cast if model has ['payment_methods' => 'array']

                // Notification Settings
                'sms_api_provider' => 'Twilio',
                'sms_api_key' => 'demo_api_key_'.rand(1000, 9999),
                'email_payment_confirmations' => (bool) rand(0, 1),
                'email_payment_reminders' => (bool) rand(0, 1),
                'email_payment_reports' => (bool) rand(0, 1),
                'sms_payment_confirmations' => (bool) rand(0, 1),
                'sms_payment_reminders' => (bool) rand(0, 1),

                // Backup & Security
                'auto_backup' => (bool) rand(0, 1),
                'two_factor_auth' => (bool) rand(0, 1),
                'session_timeout' => true,
                'login_notifications' => true,
            ]);
        }
    }
}
