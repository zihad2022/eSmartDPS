<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PakageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pakages')->insert([
            [
                'name' => 'Basic',
                'description' => 'For individuals getting started.',
                'price' => 500,
                'discount_value' => 0,
                'discount_type' => 'percentage',
                'billing_cycle' => 'monthly',
                'has_trial' => true,
                'trial_days' => 7,
                'member_limit' => 10,
                'user_limit' => 1,
                'project_limit' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'description' => 'For small teams with growing needs.',
                'price' => 1499,
                'discount_value' => 10,
                'discount_type' => 'percentage',
                'billing_cycle' => 'monthly',
                'has_trial' => true,
                'trial_days' => 7,
                'member_limit' => 100,
                'user_limit' => 10,
                'project_limit' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large organizations with custom needs.',
                'price' => 2499,
                'discount_value' => 100,
                'discount_type' => 'amount',
                'billing_cycle' => 'yearly',
                'has_trial' => true,
                'trial_days' => 7,
                'member_limit' => 0,
                'user_limit' => 0,
                'project_limit' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
