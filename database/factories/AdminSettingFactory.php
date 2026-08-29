<?php

namespace Database\Factories;

use App\Models\AdminSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminSetting>
 */
class AdminSettingFactory extends Factory
{
    protected $model = AdminSetting::class;

    public function definition(): array
    {
        return [
            'site_name' => 'eSmartDPS',
            'site_slogan' => 'Smart DPS Management System',
            'site_description' => 'A complete and modern DPS management software solution.',
            'site_keywords' => 'dps, savings, microfinance, banking',
            'currency' => 'BDT',
            'helpline_number' => '+8801700000000',
            'email_address' => 'support@esmartdps.com',
            'office_address' => 'Dhaka, Bangladesh',
            'facebook_page' => 'https://facebook.com/esmartdps',
            'late_fee' => 50,
        ];
    }
}
