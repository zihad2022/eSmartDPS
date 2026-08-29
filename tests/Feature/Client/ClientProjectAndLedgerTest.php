<?php

use App\Enums\Ledger\LedgerType;
use App\Enums\ProjectStatus;
use App\Models\Ledger;
use App\Models\LedgerCategory;
use App\Models\Project;
use App\Models\ProjectCategory;

test('client can create project category and project', function () {
    $client = createActiveClient();

    $categoryResponse = $this->actingAs($client, 'client')
        ->post(route('client.project-categories.store'), [
            'name' => 'Real Estate',
        ]);

    $categoryResponse->assertRedirect(route('client.project-categories.index'));
    $this->assertDatabaseHas('project_categories', [
        'client_id' => $client->id,
        'name' => 'Real Estate',
    ]);

    $category = ProjectCategory::where('client_id', $client->id)->first();

    $projectResponse = $this->actingAs($client, 'client')
        ->post(route('client.projects.store'), [
            'name' => 'Apartment Complex A',
            'project_category_id' => $category->id,
            'investment_amount' => 500000,
            'expected_return' => 20,
            'expected_return_type' => 'percent',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(12)->toDateString(),
            'status' => ProjectStatus::ACTIVE->value,
            'description' => 'A great residential project',
        ]);

    $projectResponse->assertRedirect(route('client.projects.index'));
    $this->assertDatabaseHas('projects', [
        'client_id' => $client->id,
        'name' => 'Apartment Complex A',
    ]);
});

test('client can list projects and view single project', function () {
    $client = createActiveClient();
    $project = Project::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($client, 'client')
        ->get(route('client.projects.index'));

    $response->assertOk()
        ->assertViewIs('client.project.index')
        ->assertSee($project->name);

    $showResponse = $this->actingAs($client, 'client')
        ->get(route('client.projects.show', $project));

    $showResponse->assertOk()
        ->assertViewIs('client.project.show')
        ->assertSee($project->name);
});

test('client can create ledger category and income/expense ledger entry', function () {
    $client = createActiveClient();

    $catResponse = $this->actingAs($client, 'client')
        ->post(route('client.ledger-categories.store'), [
            'name' => 'Office Supplies',
            'description' => 'Office related purchases',
        ]);

    $catResponse->assertRedirect(route('client.ledger-categories.index'));
    $this->assertDatabaseHas('ledger_categories', [
        'client_id' => $client->id,
        'name' => 'Office Supplies',
    ]);

    $category = LedgerCategory::where('client_id', $client->id)->first();

    $ledgerResponse = $this->actingAs($client, 'client')
        ->post(route('client.ledgers.store'), [
            'ledger_category_id' => $category->id,
            'type' => LedgerType::EXPENSE->value,
            'description' => 'Bought stationary',
            'amount' => 250,
            'entry_date' => now()->toDateString(),
        ]);

    $ledgerResponse->assertRedirect(route('client.ledgers.index'));
    $this->assertDatabaseHas('ledgers', [
        'client_id' => $client->id,
        'description' => 'Bought stationary',
        'amount' => 250,
    ]);
});

test('client can view ledger list and reports', function () {
    $client = createActiveClient();
    $category = LedgerCategory::factory()->create(['client_id' => $client->id]);
    $ledger = Ledger::factory()->create([
        'client_id' => $client->id,
        'ledger_category_id' => $category->id,
    ]);

    $indexResponse = $this->actingAs($client, 'client')
        ->get(route('client.ledgers.index'));

    $indexResponse->assertOk()
        ->assertViewIs('client.ledger.index');

    $reportResponse = $this->actingAs($client, 'client')
        ->get(route('client.ledgers.report'));

    $reportResponse->assertOk()
        ->assertViewIs('client.ledger.report');
});
