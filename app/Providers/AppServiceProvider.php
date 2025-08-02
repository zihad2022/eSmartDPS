<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Pakage;
use App\Observers\ClientObserver;
use App\Observers\PakageObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
        Model::unguard();
        // observers
        Client::observe(ClientObserver::class);
        Pakage::observe(PakageObserver::class);
    }
}
