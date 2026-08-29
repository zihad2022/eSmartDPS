<?php

use App\Models\Admin;
use App\Models\AdminSetting;
use App\Models\Client;
use App\Models\ClientPackage;
use App\Models\ClientSetting;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use App\Models\Member;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('all model factories create database records successfully', function () {
    expect(User::factory()->create())->toBeInstanceOf(User::class)
        ->and(Admin::factory()->create())->toBeInstanceOf(Admin::class)
        ->and(Client::factory()->create())->toBeInstanceOf(Client::class)
        ->and(Package::factory()->create())->toBeInstanceOf(Package::class)
        ->and(ClientPackage::factory()->create())->toBeInstanceOf(ClientPackage::class)
        ->and(Invoice::factory()->create())->toBeInstanceOf(Invoice::class)
        ->and(Payment::factory()->create())->toBeInstanceOf(Payment::class)
        ->and(Member::factory()->create())->toBeInstanceOf(Member::class)
        ->and(ProjectCategory::factory()->create())->toBeInstanceOf(ProjectCategory::class)
        ->and(Project::factory()->create())->toBeInstanceOf(Project::class)
        ->and(LedgerCategory::factory()->create())->toBeInstanceOf(LedgerCategory::class)
        ->and(Ledger::factory()->create())->toBeInstanceOf(Ledger::class)
        ->and(Ticket::factory()->create())->toBeInstanceOf(Ticket::class)
        ->and(TicketReply::factory()->create())->toBeInstanceOf(TicketReply::class)
        ->and(AdminSetting::factory()->create())->toBeInstanceOf(AdminSetting::class)
        ->and(ClientSetting::factory()->create())->toBeInstanceOf(ClientSetting::class);
});
