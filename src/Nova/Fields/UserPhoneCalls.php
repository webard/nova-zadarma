<?php

namespace Webard\NovaZadarma\Nova\Fields;

use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Nova;
use Webard\NovaZadarma\Enums\PhoneCallUserRole;
use Webard\NovaZadarma\Nova\PhoneCall;

class UserPhoneCalls
{
    public static function make($name = null, $attribute = null, $resource = null)
    {
        return BelongsToMany::make(
            $name ?? Nova::__('Phone Calls'),
            $attribute ?? 'phoneCalls',
            $resource ?? PhoneCall::class
        )
            ->fields(function () {
                return [
                    EnumBadge::make(Nova::__('Role'), 'role')
                        ->enum(PhoneCallUserRole::class)
                        ->filterable()
                        ->sortable(),
                ];
            });
    }
}
