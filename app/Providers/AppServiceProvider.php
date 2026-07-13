<?php

namespace App\Providers;

use App\Domain\Clients\Models\Client;
use App\Domain\Invoices\Models\Invoice;
use App\Domain\Packages\Models\Package;
use App\Models\Admin;
use App\Models\Ledger;
use App\Models\Member;
use App\Models\Project;
use App\Models\Ticket;
use App\Observers\Admin\ClientObserver;
use App\Observers\Admin\InvoiceObserver;
use App\Observers\Admin\PackageObserver;
use App\Observers\Admin\RoleObserver;
use App\Observers\Admin\TicketObserver;
use App\Observers\Admin\UserObserver;
use App\Observers\Client\LedgerObserver;
use App\Observers\Client\MemberObserver;
use App\Observers\Client\ProjectObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        Client::observe(ClientObserver::class);
        Package::observe(PackageObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Ticket::observe(TicketObserver::class);
        Admin::observe(UserObserver::class);
        Role::observe(RoleObserver::class);

        Member::observe(MemberObserver::class);
        Project::observe(ProjectObserver::class);
        Ledger::observe(LedgerObserver::class);

        Blade::if('adminCan', fn (string $permission): bool =>
            auth('admin')->check() && auth('admin')->user()->can($permission)
        );

        Gate::define('access-feature', function ($client, string $featureSlug): bool {
            if (! $client instanceof Client) {
                return false;
            }

            return (new \App\Services\SubscriptionService($client))->canAccessFeature($featureSlug);
        });
    }
}
