<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Member;
use App\Models\MemberShare;
use App\Models\Share;
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

        // Add 45 more dynamically
        for ($i = 6; $i <= 50; $i++) {
            $members[] = [
                'name' => "Member {$i}",
                'email' => "member{$i}@example.com",
                'phone' => '017'.str_pad($i.'000000', 8, '1', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'status' => rand(0, 1),
            ];
        }

        foreach ($members as $member) {
            $member = Member::create([
                'client_id' => $clients->random()->id,
                'name' => $member['name'],
                'email' => $member['email'],
                'phone' => $member['phone'],
                'password' => $member['password'],
                'status' => $member['status'],
            ]);

            // Create Member Share
            MemberShare::create([
                'member_id' => $member->id,
                'share_id' => Share::all()->random()->id,
                'shares_count' => rand(1, 10),
            ]);
        }
    }
}
