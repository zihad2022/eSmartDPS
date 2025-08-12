<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Package;
use App\Observers\ClientObserver;
use App\Observers\PackageObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * This method is for binding classes into the service container
     * or registering third-party packages.
     */
    public function register(): void
    {
        // No services are registered here for now.
    }

    /**
     * Bootstrap application services.
     *
     * This method is called after all services are registered.
     * Here, we configure global settings, observers, and custom Blade directives.
     */
    public function boot(): void
    {
        /**
         * 1. Enable strict mode for Eloquent models in non-production environments.
         *    This helps catch accidental lazy loading, missing attributes, and other issues.
         */
        Model::shouldBeStrict(! app()->isProduction());

        /**
         * 2. Allow mass assignment on all models.
         *    Be careful: this removes the need for `$fillable` in models.
         *    Only keep this if you trust all input sources or sanitize data properly.
         */
        Model::unguard();

        /**
         * 3. Register Eloquent model observers.
         *    Observers listen for model events like "creating", "updating", "deleting".
         */
        Client::observe(ClientObserver::class);
        Package::observe(PackageObserver::class);

        /**
         * 4. Define a custom Blade directive `@adminCan('permission-name')`.
         *    Example usage:
         *
         *        @adminCan('edit settings')
         *            <!-- Show something only if admin has permission -->
         *
         *        @endadminCan
         */
        Blade::if('adminCan', function (string $permission) {
            return auth('admin')->check()
                && auth('admin')->user()->can($permission);
        });
    }
}
