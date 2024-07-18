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
            'incoming' => 'danger',
            'outgoing' => 'success',
        ];
    }

    public static function icons(): array
    {
        return [
            'danger' => 'check-circle',
            'success' => 'exclamation-circle',
        ];
    }
}
