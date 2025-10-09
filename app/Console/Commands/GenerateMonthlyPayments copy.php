<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class GenerateMonthlyPayments extends Command
{
    protected $signature = 'payments:generate';
    protected $description = 'Generate monthly due payments for all clients.';

    public function handle(): int
    {
        $monthStart = now()->startOfMonth();
        $totalGenerated = 0;

        $clientIds = ClientSetting::pluck('client_id');

        foreach ($clientIds as $clientId) {
            $settings = ClientSetting::where('client_id', $clientId)->first();

            if (! $settings) {
                $this->warn("Skipping client {$clientId}: no settings found.");
                continue;
            }

            $members = Member::where('client_id', $clientId)->get();
            $clientGenerated = 0;

            foreach ($members as $member) {
                $dueAmount = $settings->share_price * $member->share_quantity;

                // Check if a due payment already exists for this month
                $alreadyExists = Payment::where('member_id', $member->id)
                    ->whereMonth('created_at', $monthStart->month)
                    ->whereYear('created_at', $monthStart->year)
                    ->where('status', PaymentStatus::DUE->value)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                // Create new due payment
                Payment::create([
                    'payment_id'      => generate_payment_id(),
                    'client_id'       => $member->client_id,
                    'member_id'       => $member->id,
                    'amount'          => $dueAmount,
                    'status'          => PaymentStatus::DUE->value,
                    'due_date'        => $monthStart->copy()->endOfMonth(),
                    'payment_method'  => null,   // can fill later
                    'meta'            => null,   // can store screenshot/comments later
                ]);

                $clientGenerated++;
                $totalGenerated++;
            }

            $this->info("Generated {$clientGenerated} due payments for client {$clientId}.");
        }

        $this->info("✅ Total payments generated: {$totalGenerated}");

        return Command::SUCCESS;
    }
}
