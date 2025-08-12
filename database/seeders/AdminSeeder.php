<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'ESmart Super Admin',
            'username' => 'esmartsuperadmin',
            'password' => Hash::make('superadmin123'),
            'email' => 'superadmin@mail.com',
            'phone' => '01710000001',
            'profile_photo' => null,
            'status' => true,
        ]);

        Admin::create([
            'name' => 'ESmart Admin',
            'username' => 'esmartadmin',
            'password' => Hash::make('admin123'),
            'email' => 'admin@mail.com',
            'phone' => '01710000003',
            'profile_photo' => null,
            'status' => true,
        ]);

        Admin::create([
            'name' => 'ESmart Manager',
            'username' => 'esmartmanager',
            'password' => Hash::make('manager123'),
            'email' => 'manager@mail.com',
            'phone' => '01710000002',
            'profile_photo' => null,
            'status' => true,
        ]);
        // Assign roles to users
        $superAdmin = Admin::where('username', 'esmartsuperadmin')->first();
        $admin = Admin::where('username', 'esmartadmin')->first();
        $manager = Admin::where('username', 'esmartmanager')->first();

        $superAdmin->assignRole('super-admin');
        $manager->assignRole('manager');
        $admin->assignRole('admin');
    }
}
