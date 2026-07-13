<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use Illuminate\Database\Seeder;

class BkashPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $settings = AdminSetting::query()->firstOrNew();

        $settings->fill([
            'bkash_base_url' => env('BKASH_BASE_URL'),
            'bkash_username' => env('BKASH_USERNAME'),
            'bkash_password' => env('BKASH_PASSWORD'),
            'bkash_app_key' => env('BKASH_APP_KEY'),
            'bkash_app_secret' => env('BKASH_APP_SECRET'),
            'bkash_charge' => 0,
            'bkash_status' => false,
        ])->save();
    }
}
