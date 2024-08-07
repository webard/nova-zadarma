<?php

namespace Webard\NovaZadarma\Enums;

use Webard\NovaZadarma\Traits\EnumBadgeTrait;

enum PhoneCallType: string
{
    use EnumBadgeTrait;

    case Incoming = 'incoming';
    case Outgoing = 'outgoing';

    public static function map(): array
    {
        return [
            'incoming' => 'success',
            'outgoing' => 'info',
        ];
    }

    public static function icons(): array
    {
        return [
            'info' => 'phone-outgoing',
            'success' => 'phone-incoming',
        ];
    }
}
