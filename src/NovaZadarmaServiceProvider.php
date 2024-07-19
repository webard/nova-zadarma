<?php

namespace Webard\NovaZadarma;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Http\Middleware\Authenticate;
use Laravel\Nova\Nova;
use Webard\NovaZadarma\Http\Middleware\Authorize;
use Webard\NovaZadarma\Http\Middleware\ZadarmaWebhookVerify;
use Webard\NovaZadarma\Nova\PhoneCall;
use Webard\NovaZadarma\Services\ZadarmaService;

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
            $sipField = config('nova-zadarma.models.user.sip_field');

            return $user !== null && $user->$sipField !== null;
        });

        if ($this->app->config->get('nova-zadarma.nova_resources.phone_call.register', false)) {
            Nova::resources([PhoneCall::class]);
        }
    }

    public function provides()
    {
        return [ZadarmaService::class];
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

        Route::middleware(['nova', ZadarmaWebhookVerify::class])
            ->prefix('nova-vendor/webard/nova-zadarma/webhook')
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

        $this->app->bind(ZadarmaService::class, function ($app) {
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
