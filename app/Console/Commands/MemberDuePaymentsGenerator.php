<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClientSetting;
use App\Models\Member;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class MemberDuePaymentsGenerator extends Command
{
    protected $signature = 'payments:generator';
    protected $description = 'Generate due payments for all clients';

    public function handle()
    {
        $today = now()->startOfMonth();
        $totalGenerated = 0;

        // 🔹 Get all clients
        $clients = ClientSetting::pluck('client_id');

        foreach ($clients as $clientId) {
            $settings = ClientSetting::where('client_id', $clientId)->first();

            if (! $settings) {
                $this->warn("⚠️ Skipping client {$clientId}, no settings found.");
                continue;
            }

            $members = Member::where('client_id', $clientId)->get();
            $generatedCount = 0;

            foreach ($members as $member) {
                // 🔹 Calculate due amount based on share price & quantity
                $dueAmount = $settings->share_price * $member->share_quantity;

                // 🔹 Skip if due payment already exists this month
                $alreadyExists = Payment::where('member_id', $member->id)
                    ->whereMonth('created_at', $today->month)
                    ->whereYear('created_at', $today->year)
                    ->where('status', PaymentStatus::DUE->value)
                    ->exists();

                if ($alreadyExists) continue;

                // 🔹 Create new due payment
                Payment::create([
                    'client_id'       => $member->client_id,
                    'payment_id'      => generate_payment_id(),
                    'member_id'       => $member->id,
                    'amount'          => $dueAmount,
                    'currency'        => 'USD',              // default currency
                    'payment_method'  => null,               // e.g., cash, card
                    'transaction_id'  => null,               // payment gateway reference
                    'reference'       => null,               // optional internal reference
                    'status'          => PaymentStatus::DUE->value,
                    'paid_at'         => null,               // will be set when paid
                    'due_date'        => $today->copy()->endOfMonth(), // optional due date
                    'meta'            => null,               // extra info if needed
                ]);

                $generatedCount++;
                $totalGenerated++;
            }

            $this->info("✅ Generated {$generatedCount} due payments for client {$clientId}.");
        }

        $this->info("🎉 Total due payments generated: {$totalGenerated}.");
        return Command::SUCCESS;
    }
}
