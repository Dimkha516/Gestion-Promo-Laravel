<?php

namespace App\Providers;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->singleton('firebase.database', function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials'))
                ->withDatabaseUri(config('services.firebase.database_url'));

            return $factory->createDatabase();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
