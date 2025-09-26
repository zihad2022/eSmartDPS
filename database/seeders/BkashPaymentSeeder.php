<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use Illuminate\Database\Seeder;

class BkashPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminSetting::create([
            'bkash_base_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout',
            'bkash_username' => 'your_bkash_username',
            'bkash_password' => 'your_bkash_password',
            'bkash_app_key' => 'your_bkash_app_key',
            'bkash_app_secret' => 'your_bkash_app_secret',
            'bkash_charge' => 0,
            'bkash_sandbox' => true,
        ]);
    }
}
