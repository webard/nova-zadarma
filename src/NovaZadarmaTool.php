<?php

namespace Webard\NovaZadarma;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Zadarma_API\ApiException;

class NovaZadarmaTool extends Tool
{
    public function boot(): void
    {
        $enabled = config('nova-zadarma.enabled');

        if (! $enabled) {
            return;
        }

        $sipLogin = config('nova-zadarma.auth.sip_login');

        $user = auth()->user();

        if ($user === null) {
            return;
        }

        $sipField = config('nova-zadarma.sip_field');
        $userSip = $user->$sipField;

        if ($userSip === null) {
            return;
        }

        $canCall = Gate::allows('zadarmaCall', $user);

        if (! $canCall) {
            return;
        }

        [$key, $login] = Cache::remember('zadarma_webrtc_key3_'.$userSip, 60, function () use ($userSip, $sipLogin) {
            $zadarmaService = app('zadarma');

            try {
                $key = $zadarmaService->getWebrtcKey($userSip);
                $login = $zadarmaService->getWebrtcLogin($sipLogin, $userSip);
            } catch (ApiException $e) {
                $key = null;
                $login = null;
            }

            return [$key, $login];
        });

        Nova::provideToScript([
            'zadarma_can_call' => $canCall,
            'zadarma_key' => $key,
            'zadarma_login' => $login,
        ]);

        Nova::script('current-phone-call', __DIR__.'/../dist/js/tool.js');
        Nova::style('nova-zadarma', __DIR__.'/../dist/css/asset.css');

    }

    public function menu(Request $request): mixed
    {
        return [];
    }
}
