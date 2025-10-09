<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $members = Member::with('client')->get();

        if ($members->isEmpty()) {
            $this->command->error('No members found. Please seed members first.');
            return;
        }

        $statuses = [PaymentStatus::PAID->value, PaymentStatus::PENDING->value, PaymentStatus::FAILED->value];
        $methods = [1, 2, 3];

        $paymentCount = 1;

        foreach ($members as $member) {
            $paymentCountForMember = rand(1, 3);

            for ($i = 0; $i < $paymentCountForMember; $i++) {
                $status = $statuses[array_rand($statuses)];

                Payment::create([
                    'payment_id'      => '#PAY' . str_pad($paymentCount++, 4, '0', STR_PAD_LEFT),
                    'client_id'       => $member->client_id,
                    'member_id'       => $member->id,
                    'amount'          => rand(100, 1000),
                    'payment_method'  => $methods[array_rand($methods)],
                    'transaction_id'  => Str::upper(Str::random(10)),
                    'reference'       => 'REF-' . rand(10000, 99999),
                    'status'          => $status,
                    'paid_at'         => $status === PaymentStatus::PAID->value ? now()->subDays(rand(0, 15)) : null,
                    'due_date'        => now()->addDays(rand(3, 10)),
                    'meta'            => [
                        'notes' => fake()->sentence(),
                        'created_by' => 'Seeder',
                    ],
                ]);
            }
        }

        $this->command->info('Payments seeded successfully with realistic data.');
    }
}
