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
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'password' => Hash::make('superadmin123'),
            'email' => 'superadmin@mail.com',
            'phone' => '01710000001',
            'profile_photo' => null,
            'role' => 'admin',
            'status' => true,
        ]);

        Admin::create([
            'name' => 'Branch Manager',
            'username' => 'branchmanager',
            'password' => Hash::make('manager123'),
            'email' => 'manager@mail.com',
            'phone' => '01710000002',
            'profile_photo' => null,
            'role' => 'manager',
            'status' => true,
        ]);

        Admin::create([
            'name' => 'Content Editor',
            'username' => 'contenteditor',
            'password' => Hash::make('editor123'),
            'email' => 'editor@mail.com',
            'phone' => '01710000003',
            'profile_photo' => null,
            'role' => 'editor',
            'status' => true,
        ]);
    }
}
