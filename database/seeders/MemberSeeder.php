<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::where('parent_id', null)->get();
        $members = [
            [
                'name' => 'Zihadul Islam',
                'email' => 'zihadul@example.com',
                'phone' => '01711111111',
                'password' => Hash::make('password'),
                'status' => rand(0, 1),
            ],
            [
                'name' => 'Amina Khatun',
                'email' => 'amina@example.com',
                'phone' => '01722222222',
                'password' => Hash::make('password'),
                'status' => rand(0, 1),
            ],
            [
                'name' => 'Rahim Uddin',
                'email' => 'rahim@example.com',
                'phone' => '01733333333',
                'password' => Hash::make('password'),
                'status' => rand(0, 1),
            ],
            [
                'name' => 'Tania Rahman',
                'email' => 'tania@example.com',
                'phone' => '01744444444',
                'password' => Hash::make('password'),
                'status' => rand(0, 1),
            ],
            [
                'name' => 'Imran Hossain',
                'email' => 'imran@example.com',
                'phone' => '01755555555',
                'password' => Hash::make('password'),
                'status' => rand(0, 1),
            ],
        ];

        foreach ($members as $member) {
            $member = Member::create([
                'client_id' => $clients->random()->id,
                'name' => $member['name'],
                'email' => $member['email'],
                'phone' => $member['phone'],
                'password' => $member['password'],
                'status' => $member['status'],
                'share_quantity' => rand(1, 10),
                'total_balance' => rand(100, 1000),
            ]);
        }
    }
}
