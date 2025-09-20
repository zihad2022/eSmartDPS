<?php

namespace Database\Seeders;

use App\Enums\Package\BillingCycle;
use App\Enums\Package\DiscountType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('packages')->insert([
            [
                'name' => 'Basic',
                'description' => 'For individuals getting started.',
                'price' => 500,
                'discount_value' => 0,
                'discount_type' => DiscountType::PERCENT,
                'billing_cycle' => BillingCycle::MONTHLY,
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
                'discount_type' => DiscountType::PERCENT,
                'billing_cycle' => BillingCycle::MONTHLY,
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
                'discount_type' => DiscountType::FIXED,
                'billing_cycle' => BillingCycle::YEARLY,
                'has_trial' => true,
                'trial_days' => 7,
                'member_limit' => 500,
                'user_limit' => 50,
                'project_limit' => 200,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Starter',
                'description' => 'Best for freelancers and hobby projects.',
                'price' => 299,
                'discount_value' => 0,
                'discount_type' => DiscountType::FIXED,
                'billing_cycle' => BillingCycle::MONTHLY,
                'has_trial' => false,
                'trial_days' => 0,
                'member_limit' => 5,
                'user_limit' => 1,
                'project_limit' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Business',
                'description' => 'For growing businesses that need scalability.',
                'price' => 1999,
                'discount_value' => 15,
                'discount_type' => DiscountType::PERCENT,
                'billing_cycle' => BillingCycle::MONTHLY,
                'has_trial' => true,
                'trial_days' => 14,
                'member_limit' => 200,
                'user_limit' => 25,
                'project_limit' => 100,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'description' => 'Advanced features and higher limits for professionals.',
                'price' => 2999,
                'discount_value' => 20,
                'discount_type' => DiscountType::PERCENT,
                'billing_cycle' => BillingCycle::YEARLY,
                'has_trial' => true,
                'trial_days' => 30,
                'member_limit' => 300,
                'user_limit' => 40,
                'project_limit' => 150,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}