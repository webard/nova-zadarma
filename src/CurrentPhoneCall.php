<?php

namespace Sylapi\NovaPhoneCallModal;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class CurrentPhoneCall extends Tool
{

    public function boot(): void
    {
        Nova::script('current-phone-call', __DIR__.'/../dist/js/tool.js');
        Nova::provideToScript([]);
    }


    public function menu(Request $request): mixed
    {
        return null;
    }
}