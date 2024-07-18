<?php

namespace Webard\NovaZadarma\Enums;

use Webard\NovaZadarma\Traits\EnumBadgeTrait;

enum PhoneCallUserRole: string
{
    use EnumBadgeTrait;

    case Caller = 'caller';
    case Receiver = 'receiver';

    public static function map(): array
    {
        return [
            'caller' => 'info',
            'receiver' => 'success',
        ];
    }
}
