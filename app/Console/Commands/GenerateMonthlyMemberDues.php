<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Console\Command;

class GenerateMonthlyMemberDues extends Command
{
    protected $signature = 'payment:generate-monthly-member-dues';

    protected $description = 'Command description';

    public function handle()
    {
        $today = now()->startOfMonth();
        $dueAmount = 500;

        $members = Member::orderBy('client_id')->get();

        // Track serial numbers per client based on existing DUE payments
        $serialMap = [];

        foreach ($members as $member) {
            $clientId = $member->client_id;

            // Skip if due already exists for this member this month
            $alreadyExists = Payment::where('member_id', $member->id)
                ->whereMonth('date', $today->month)
                ->whereYear('date', $today->year)
                ->where('status', PaymentStatus::DUE->value)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            // Get starting serial for this client if not set
            if (! isset($serialMap[$clientId])) {
                $existingCount = Payment::where('client_id', $clientId)
                    ->where('status', PaymentStatus::DUE->value)
                    ->count();

                $serialMap[$clientId] = $existingCount;
            }

            // Increment serial
            $serialMap[$clientId]++;
            $serial = $serialMap[$clientId];

            // Create payment ID
            $paymentId = 'PAY-'.$serial;

            // Create the payment record
            Payment::create([
                'client_id' => $clientId,
                'payment_id' => $paymentId,
                'member_id' => $member->id,
                'amount' => $dueAmount,
                'date' => $today,
                'payment_method' => null,
                'status' => PaymentStatus::DUE->value,
            ]);
        }

        $this->info("Due payments generated for {$members->count()} members.");

        return Command::SUCCESS;
    }
}
