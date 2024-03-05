<?php

namespace Webard\NovaZadarma;

use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Http\Middleware\Authenticate;
use Webard\NovaZadarma\Services\ZadarmaService;
use Webard\NovaZadarma\Http\Middleware\Authorize;

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

        $this->publishes([
            __DIR__.'/../config/nova-zadarma.php' => config_path('nova-zadarma.php'),
        ], 'config');

        Nova::serving(function (ServingNova $event) {

            Nova::script('nova-zadarma', __DIR__.'/../dist/js/asset.js');

            Nova::style('nova-zadarma', __DIR__.'/../dist/css/asset.css');

            $zdarmaService = app(ZadarmaService::class);

            $sipField = config('nova-zadarma.sip_field');

            $userSip = auth()->user()->{$sipField} ?? null;

           

            $canCall = Gate::allows('zadarmaCall', auth()->user());

            if ($canCall === true && ! empty($userSip)) {
                $key = $zdarmaService->getWebrtcKey($userSip);
                $login = $zdarmaService->getWebrtcLogin($userSip);
            }

            Log::warning('NOVA ZADARMA', [
                'user' => $userSip,
                'sipField' => $sipField,
                'canCall' => $canCall,
                'key' => $key ?? 'null',
                'login' => $login ?? 'null'
            ]);

            Nova::provideToScript([
                'zadarma_can_call' => $canCall ?? false,
                'zadarma_key' => $key ?? 'brak',
                'zadarma_login' => $login ?? 'brak',
            ]);
        });

        Gate::define('zadarmaCall', function ($user = null) {
            $sipField = config('nova-zadarma.sip_field');

            return $user !== null && $user->$sipField !== null;
        });
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
        //
    }
}
