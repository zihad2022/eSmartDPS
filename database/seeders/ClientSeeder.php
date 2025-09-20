<?php

namespace Database\Seeders;

use App\Enums\Package\BillingCycle;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Create one main super_admin client
        $owner = Client::create([
            'parent_id'   => null,
            'user_id'     => generate_client_user_id(),
            'password'    => Hash::make('owner1234'),
            'first_name'  => 'Main Company',
            'last_name'   => 'Owner',
            'email'       => 'admin@maincompany.com',
            'phone'       => '01710000001',
            'division'    => 'Dhaka',
            'district'    => 'Dhaka',
            'address'     => 'Main office of Main Company',
            'postal_code' => '1000',
            'role'        => 'super_admin',
            'status'      => true,
        ]);

        // ✅ Manager under super_admin
        Client::create([
            'parent_id'   => $owner->id,
            'user_id'     => generate_client_user_id(),
            'password'    => Hash::make('manager1234'),
            'first_name'  => 'Manager',
            'last_name'   => 'Main Company',
            'email'       => 'manager@maincompany.com',
            'phone'       => '01720000001',
            'division'    => 'Dhaka',
            'district'    => 'Dhaka',
            'address'     => 'Branch office of Main Company',
            'postal_code' => '2000',
            'role'        => 'manager',
            'status'      => true,
        ]);

        // ✅ Editor under super_admin
        Client::create([
            'parent_id'   => $owner->id,
            'user_id'     => generate_client_user_id(),
            'password'    => Hash::make('editor1234'),
            'first_name'  => 'Editor',
            'last_name'   => 'Main Company',
            'email'       => 'editor@maincompany.com',
            'phone'       => '01730000001',
            'division'    => 'Dhaka',
            'district'    => 'Dhaka',
            'address'     => 'Content division of Main Company',
            'postal_code' => '3000',
            'role'        => 'editor',
            'status'      => true,
        ]);

        // ✅ Subscription for super_admin
        $package = Package::first();

        ClientPackage::create([
            'client_id'  => $owner->id,
            'package_id' => $package->id,
            'starts_at'  => now(),
            'ends_at'    => $package->billing_cycle == BillingCycle::MONTHLY
                ? now()->addMonth()
                : ($package->billing_cycle == BillingCycle::YEARLY
                    ? now()->addYear()
                    : now()->addYear()), // fallback
            'is_trial'   => $package->has_trial ? true : false,
            'is_active'  => $package->is_active,
        ]);
    }
}
