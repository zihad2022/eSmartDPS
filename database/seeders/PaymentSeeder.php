<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $members = Member::all();

        if ($members->isEmpty()) {
            $this->command->error('No members found in the database. Please seed members first.');

            return;
        }

        $methods = [1, 2, 3, 4, 5];
        $statuses = [1, 2, 3];

        $paymentCount = 1;

        foreach ($members as $member) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                Payment::create([
                    'payment_id' => '#PAY'.str_pad($paymentCount++, 3, '0', STR_PAD_LEFT),
                    'member_id' => $member->id,
                    'amount' => rand(100, 1000),
                    'date' => now()->subDays(rand(0, 30))->toDateString(),
                    'payment_method' => $methods[array_rand($methods)],
                    'status' => $statuses[array_rand($statuses)],
                ]);
            }
        }

        $this->command->info('Payments seeded successfully for real members.');
    }
}
