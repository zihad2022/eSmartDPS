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
    protected $description = 'Generate payments every 2 minutes if not already created in the last 2-minute window.';

    public function handle(): int
    {
        $currentTime = now();
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

                // Check if a payment for this member already exists within the last 2 minutes
                $alreadyExists = Payment::where('member_id', $member->id)
                    ->where('created_at', '>=', $currentTime->copy()->subMinutes(2))
                    ->where('created_at', '<=', $currentTime)
                    ->where('status', PaymentStatus::DUE->value)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                // Create new payment for this 2-minute window
                Payment::create([
                    'payment_id'      => generate_payment_id(),
                    'client_id'       => $member->client_id,
                    'member_id'       => $member->id,
                    'amount'          => $dueAmount,
                    'status'          => PaymentStatus::DUE->value,
                    'due_date'        => $currentTime->copy()->addMinutes(2), // optional: set due date in 2 minutes
                    'payment_method'  => null, // can fill later
                    'meta'            => null, // optional comments/screenshots
                ]);

                $clientGenerated++;
                $totalGenerated++;
            }

            $this->info("Generated {$clientGenerated} payments for client {$clientId} within 2-minute window ending at {$currentTime->format('H:i')}");
        }

        $this->info("✅ Total payments generated this run: {$totalGenerated}");

        return Command::SUCCESS;
    }
}
