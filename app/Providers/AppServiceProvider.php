<?php

namespace App\Providers;

use App\Domain\Clients\Models\Client;
use App\Domain\Invoices\Models\Invoice;
use App\Models\Ledger;
use App\Models\Member;
use App\Domain\Packages\Models\Package;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Observers\Admin\ClientObserver;
use App\Observers\Admin\InvoiceObserver;
use App\Observers\Admin\PackageObserver;
use App\Observers\Admin\TicketObserver;
use App\Observers\Admin\UserObserver;
use App\Observers\Client\LedgerObserver;
use App\Observers\Client\MemberObserver;
use App\Observers\Client\ProjectObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        // Strict mode for non-production environments
        Model::shouldBeStrict(!app()->isProduction());

        // Disable mass-assignment protection
        Model::unguard();

        // Register model observers
        Client::observe(ClientObserver::class);
        Package::observe(PackageObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Ticket::observe(TicketObserver::class);
        User::observe(UserObserver::class);

        Member::observe(MemberObserver::class);
        Project::observe(ProjectObserver::class);
        Ledger::observe(LedgerObserver::class);

        // Custom Blade directive: @adminCan('permission')
        Blade::if('adminCan', function (string $permission) {
            return auth('admin')->check()
                && auth('admin')->user()->can($permission);
        });

        // Global Gate for Subscription Feature Access
        \Illuminate\Support\Facades\Gate::define('access-feature', function ($client, string $featureSlug) {
            if (!($client instanceof \App\Domain\Clients\Models\Client)) {
                return false;
            }
            return (new \App\Services\SubscriptionService($client))->canAccessFeature($featureSlug);
        });
    }
}
