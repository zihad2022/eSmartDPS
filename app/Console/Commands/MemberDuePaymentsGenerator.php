<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatus;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Share;
use Illuminate\Console\Command;

class MemberDuePaymentsGenerator extends Command
{
    protected $signature = 'payments:generator';

    protected $description = 'Generate due payments for members';

    public function handle()
    {
        $today = now()->startOfMonth();

        $members = Member::with(['memberShares.share'])->get();

        $generatedCount = 0;

        foreach ($members as $member) {
            foreach ($member->memberShares as $memberShare) {
                // Calculate due amount = share price * shares_count
                $dueAmount = $memberShare->share->price * $memberShare->shares_count;

                // Check if due already exists for this member+share this month
                $alreadyExists = Payment::where('member_id', $member->id)
                    ->where('member_shares_id', $memberShare->id)
                    ->whereMonth('created_at', $today->month)
                    ->whereYear('created_at', $today->year)
                    ->where('status', PaymentStatus::DUE->value)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                Payment::create([
                    'client_id' => $member->client_id,
                    'payment_id' => generate_payment_id(),
                    'member_id' => $member->id,
                    'member_shares_id' => $memberShare->id,
                    'amount' => $dueAmount,
                    'payment_method' => null,
                    'status' => PaymentStatus::DUE->value,
                ]);

                $generatedCount++;
            }
        }

        $this->info("Due payments generated for {$generatedCount} records.");

        return Command::SUCCESS;
    }
}
