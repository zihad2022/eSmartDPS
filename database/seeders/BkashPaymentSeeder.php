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
            'bkash_base_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta',
            'bkash_username' => 'sandboxTokenizedUser02',
            'bkash_password' => 'sandboxTokenizedUser02@12345',
            'bkash_app_key' => '4f6o0cjiki2rfm34kfdadl1eqq',
            'bkash_app_secret' => '2is7hdktrekvrbljjh44ll3d9l1dtjo4pasmjvs5vl5qr3fug4b',
            'bkash_charge' => 0,
            'bkash_status' => true,
        ])->save();
    }
}
