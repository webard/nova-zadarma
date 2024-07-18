<?php

namespace Webard\NovaZadarma;

use Illuminate\Support\Facades\Cache;
use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Exceptions\NovaException;
use Laravel\Nova\Http\Middleware\Authenticate;
use Webard\NovaZadarma\Services\ZadarmaService;
use Webard\NovaZadarma\Http\Middleware\Authorize;
use Webard\NovaZadarma\Exceptions\ZadarmaServiceException;
use Zadarma_API\ApiException;

class NovaZadarmaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Gate::define('zadarmaCall', function ($user = null) {
            $sipField = config('nova-zadarma.sip_field');

            return $user !== null && $user->$sipField !== null;
        });
    }

    public function provides()
    {
        return ['zadarma'];
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authenticate::class, Authorize::class], 'nova-zadarma')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/webard/nova-zadarma')
            ->group(__DIR__.'/../routes/api.php');

        Route::middleware(['nova'])
            ->prefix('nova-vendor/webard/nova-zadarma')
            ->group(__DIR__.'/../routes/webhook.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->publish();
        }

        $this->app->bind('zadarma', function ($app) {
            $key = $app->config->get('nova-zadarma.auth.key');
            $secret = $app->config->get('nova-zadarma.auth.secret');

            return new ZadarmaService($key, $secret);
        });

        $this->mergeConfigFrom(__DIR__.'/../config/nova-zadarma.php', 'nova-zadarma');
    }

    protected function publish(): void
    {
        $this->publishes([
            __DIR__.'/../config/nova-zadarma.php' => config_path('nova-zadarma.php'),
        ], 'config');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ]);


    }
}
