<?php

use App\Enums\Ledger\LedgerType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Admin;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use App\Models\Member;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Ticket;
use App\Models\TicketReply;

test('admin active and inactive scopes filter properly', function () {
    $active = Admin::factory()->create(['status' => true]);
    $inactive = Admin::factory()->inactive()->create();

    expect(Admin::active()->pluck('id'))->toContain($active->id)
        ->and(Admin::active()->pluck('id'))->not->toContain($inactive->id)
        ->and(Admin::inactive()->pluck('id'))->toContain($inactive->id);
});

test('member relations and balance calculations', function () {
    $client = Client::factory()->create();
    $member = Member::factory()->create([
        'client_id' => $client->id,
        'share_quantity' => 10,
        'total_balance' => 25000,
    ]);

    expect($member->client->id)->toBe($client->id)
        ->and($member->share_quantity)->toBe(10)
        ->and($member->total_balance)->toBe(25000);
});

test('project relationships, slug generation and progress percent', function () {
    $client = Client::factory()->create();
    $category = ProjectCategory::factory()->create(['client_id' => $client->id]);

    $project = Project::factory()->create([
        'client_id' => $client->id,
        'project_category_id' => $category->id,
        'name' => 'Solar Power Plant',
        'slug' => 'solar-power-plant',
        'status' => ProjectStatus::ACTIVE,
        'start_date' => now()->subDays(10),
        'end_date' => now()->addDays(10),
    ]);

    expect($project->slug)->toBe('solar-power-plant')
        ->and($project->projectCategory->id)->toBe($category->id)
        ->and($project->client->id)->toBe($client->id)
        ->and($project->status)->toBe(ProjectStatus::ACTIVE)
        ->and($project->progress_percent)->toBeGreaterThanOrEqual(40)
        ->and($project->progress_percent)->toBeLessThanOrEqual(60);
});

test('payment relationships, status casting and helpers', function () {
    $client = Client::factory()->create();
    $member = Member::factory()->create(['client_id' => $client->id]);

    $payment = Payment::factory()->create([
        'client_id' => $client->id,
        'member_id' => $member->id,
        'amount' => 5000,
        'payment_method' => PaymentMethod::ONLINE,
        'status' => PaymentStatus::PAID,
    ]);

    expect($payment->client->id)->toBe($client->id)
        ->and($payment->member->id)->toBe($member->id)
        ->and($payment->amount)->toBe(5000)
        ->and($payment->isPaid())->toBeTrue()
        ->and($payment->isDue())->toBeFalse()
        ->and(Payment::paid()->pluck('id'))->toContain($payment->id);
});

test('ledger income and expense scopes work properly', function () {
    $client = Client::factory()->create();
    $category = LedgerCategory::factory()->create(['client_id' => $client->id]);

    $income = Ledger::factory()->create([
        'client_id' => $client->id,
        'ledger_category_id' => $category->id,
        'type' => LedgerType::INCOME,
        'amount' => 15000,
    ]);

    $expense = Ledger::factory()->create([
        'client_id' => $client->id,
        'ledger_category_id' => $category->id,
        'type' => LedgerType::EXPENSE,
        'amount' => 5000,
    ]);

    expect(Ledger::income()->pluck('id'))->toContain($income->id)
        ->and(Ledger::income()->pluck('id'))->not->toContain($expense->id)
        ->and(Ledger::expense()->pluck('id'))->toContain($expense->id);
});

test('ticket and ticket replies relationship and enum casting', function () {
    $client = Client::factory()->create();
    $ticket = Ticket::factory()->create([
        'client_id' => $client->id,
        'status' => TicketStatus::OPEN,
        'priority' => TicketPriority::HIGH,
    ]);

    $reply = TicketReply::factory()->create([
        'ticket_id' => $ticket->id,
        'client_id' => $client->id,
        'admin_id' => null,
        'message' => 'Need quick update please',
    ]);

    expect($ticket->client->id)->toBe($client->id)
        ->and($ticket->status)->toBe(TicketStatus::OPEN)
        ->and($ticket->priority)->toBe(TicketPriority::HIGH)
        ->and($ticket->replies)->toHaveCount(1)
        ->and($reply->isFromClient())->toBeTrue()
        ->and($reply->isFromAdmin())->toBeFalse()
        ->and($ticket->replies->first()->message)->toBe('Need quick update please');
});

test('client package active status and relations', function () {
    $client = Client::factory()->create();
    $package = Package::factory()->create();

    $subscription = ClientPackage::factory()->create([
        'client_id' => $client->id,
        'package_id' => $package->id,
        'is_active' => true,
        'status' => ClientPackage::STATUS_ACTIVE,
        'starts_at' => now()->subDays(5),
        'ends_at' => now()->addDays(25),
    ]);

    expect($subscription->client->id)->toBe($client->id)
        ->and($subscription->package->id)->toBe($package->id)
        ->and($subscription->isActive())->toBeTrue()
        ->and(ClientPackage::active()->pluck('id'))->toContain($subscription->id);
});
