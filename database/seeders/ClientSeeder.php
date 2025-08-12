<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $ownerData = [
            [
                'company' => 'TechSoft Ltd.',
                'username' => 'techsoft001',
                'email' => 'admin@techsoft.com',
                'phone' => '01710000001',
                'division' => 'Dhaka',
                'district' => 'Dhaka',
            ],
            [
                'company' => 'GreenMart Retail',
                'username' => 'greenmart001',
                'email' => 'admin@greenmart.com',
                'phone' => '01710000002',
                'division' => 'Chattogram',
                'district' => 'Chattogram',
            ],
            [
                'company' => 'BuildHub Constructions',
                'username' => 'buildhub001',
                'email' => 'admin@buildhub.com',
                'phone' => '01710000003',
                'division' => 'Khulna',
                'district' => 'Khulna',
            ],
            [
                'company' => 'AgroFresh Ltd.',
                'username' => 'agrofresh001',
                'email' => 'admin@agrofresh.com',
                'phone' => '01710000004',
                'division' => 'Sylhet',
                'district' => 'Sylhet',
            ],
            [
                'company' => 'NextGen IT Solutions',
                'username' => 'nextgen001',
                'email' => 'admin@nextgen.com',
                'phone' => '01710000005',
                'division' => 'Rajshahi',
                'district' => 'Rajshahi',
            ],
        ];

        $createdOwners = collect();

        foreach ($ownerData as $index => $data) {
            // ✅ Create Owner
            $owner = Client::create([
                'parent_id' => null,
                'user_id' => generate_client_user_id(),
                'password' => Hash::make('owner1234'),
                'first_name' => $data['company'],
                'last_name' => 'Owner',
                'email' => $data['email'],
                'phone' => $data['phone'],
                'division' => $data['division'],
                'district' => $data['district'],
                'address' => 'Main office of '.$data['company'],
                'postal_code' => '100'.($index + 1),
                'role' => 'admin',
                'status' => true,
            ]);

            $createdOwners->push($owner);

            // ✅ Manager always belongs to the owner being created
            Client::create([
                'parent_id' => $owner->id, // ✅ FIXED
                'user_id' => generate_client_user_id(),
                'password' => Hash::make('manager1234'),
                'first_name' => 'Manager',
                'last_name' => $data['company'],
                'email' => 'manager'.($index + 1).'@'.Str::slug($data['company']).'.com',
                'phone' => '0172000000'.($index + 1),
                'division' => $data['division'],
                'district' => $data['district'],
                'address' => 'Branch office of '.$data['company'],
                'postal_code' => '200'.($index + 1),
                'role' => 'manager',
                'status' => true,
            ]);

            // ✅ Editor belongs to a random owner from the created owners
            Client::create([
                'parent_id' => $createdOwners->random()->id, // ✅ FIXED
                'user_id' => generate_client_user_id(),
                'password' => Hash::make('editor1234'),
                'first_name' => 'Editor',
                'last_name' => $data['company'],
                'email' => 'editor'.($index + 1).'@'.Str::slug($data['company']).'.com',
                'phone' => '0173000000'.($index + 1),
                'division' => $data['division'],
                'district' => $data['district'],
                'address' => 'Content division of '.$data['company'],
                'postal_code' => '300'.($index + 1),
                'role' => 'editor',
                'status' => rand(0, 1),
            ]);

            // ✅ Subscription for owner
            ClientPackage::create([
                'client_id' => $owner->id,
                'package_id' => rand(1, 3),
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
                'is_trial' => false,
                'is_active' => true,
            ]);
        }
    }
}
