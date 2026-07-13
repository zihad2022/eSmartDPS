<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'role' => 'super-admin',
                'name' => 'ESmart Super Admin',
                'username' => 'esmartsuperadmin',
                'email' => 'superadmin@mail.com',
                'password' => 'password',
                'phone' => '01710000001',
            ],
            [
                'role' => 'admin',
                'name' => 'ESmart Admin',
                'username' => 'esmartadmin',
                'email' => 'admin@mail.com',
                'password' => 'password',
                'phone' => '01710000003',
            ],
            [
                'role' => 'manager',
                'name' => 'ESmart Manager',
                'username' => 'esmartmanager',
                'email' => 'manager@mail.com',
                'password' => 'password',
                'phone' => '01710000002',
            ],
        ];

        foreach ($accounts as $account) {
            $role = $account['role'];
            unset($account['role']);

            $admin = Admin::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'phone' => $account['phone'],
                    'password' => Hash::make($account['password']),
                    'status' => true,
                ],
            );

            $admin->syncRoles([$role]);
        }
    }

    private function seedPassword(string $environmentKey): string
    {
        $password = trim((string) env($environmentKey, ''));

        if ($password !== '') {
            return $password;
        }

        if (app()->environment(['local', 'testing'])) {
            return 'password';
        }

        throw new RuntimeException(
            "Set [{$environmentKey}] before running AdminSeeder outside local/testing environments."
        );
    }
}
