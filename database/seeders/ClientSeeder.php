<?php

namespace Database\Seeders;

use App\Enums\Package\BillingCycle;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $now = now();
            $defaultPassword = Hash::make('password');

            // Create one main super_admin client
            $owner = Client::create([
                'parent_id'   => null,
                'user_id'     => generate_client_user_id(),
                'password'    => $defaultPassword,
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

            // Child clients under super_admin
            $children = [
                [
                    'first_name'  => 'Manager',
                    'last_name'   => 'Main Company',
                    'email'       => 'manager@maincompany.com',
                    'phone'       => '01720000001',
                    'address'     => 'Branch office of Main Company',
                    'postal_code' => '2000',
                    'role'        => 'manager',
                ],
                [
                    'first_name'  => 'Editor',
                    'last_name'   => 'Main Company',
                    'email'       => 'editor@maincompany.com',
                    'phone'       => '01730000001',
                    'address'     => 'Content division of Main Company',
                    'postal_code' => '3000',
                    'role'        => 'editor',
                ],
            ];

            foreach ($children as $c) {
                Client::create([
                    'parent_id'   => $owner->id,
                    'user_id'     => generate_client_user_id(),
                    'password'    => $defaultPassword,
                    'first_name'  => $c['first_name'],
                    'last_name'   => $c['last_name'],
                    'email'       => $c['email'],
                    'phone'       => $c['phone'],
                    'division'    => 'Dhaka',
                    'district'    => 'Dhaka',
                    'address'     => $c['address'],
                    'postal_code' => $c['postal_code'],
                    'role'        => $c['role'],
                    'status'      => true,
                ]);
            }

            // Subscription for super_admin
            $package = Package::first();
            if ($package) {
                $endsAt = match ($package->billing_cycle) {
                    BillingCycle::MONTHLY => $now->copy()->addMonth(),
                    BillingCycle::YEARLY  => $now->copy()->addYear(),
                    default               => $now->copy()->addYear(),
                };

                ClientPackage::create([
                    'client_id'  => $owner->id,
                    'package_id' => $package->id,
                    'starts_at'  => $now,
                    'ends_at'    => $endsAt,
                    'is_trial'   => (bool) $package->has_trial,
                    'is_active'  => (bool) $package->is_active,
                ]);
            }
        });
    }
}
