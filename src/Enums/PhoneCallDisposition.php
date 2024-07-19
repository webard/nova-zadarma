<?php

namespace Webard\NovaZadarma\Enums;

use Webard\NovaZadarma\Traits\EnumBadgeTrait;

enum PhoneCallDisposition: string
{
    use EnumBadgeTrait;

    case Pending = 'pending';
    case Answered = 'answered';
    case Busy = 'busy';
    case Cancel = 'cancel';
    case NoAnswer = 'no answer';
    case Failed = 'failed';
    case NoMoney = 'no money';
    case UnallocatedNumber = 'unallocated number';
    case NoLimit = 'no limit';
    case NoDayLimit = 'no day limit';
    case LineLimit = 'line limit';
    case NoMoneyNoLimit = 'no money, no limit';

    public static function map(): array
    {
        return [
            self::Pending->value() => 'info',
            'answered' => 'success',
            'busy' => 'warning',
            'cancel' => 'warning',
            'no answer' => 'warning',
            'failed' => 'warning',
            'no money' => 'danger',
            'unallocated number' => 'danger',
            'no limit' => 'danger',
            'no day limit' => 'danger',
            'line limit' => 'danger',
            'no money, no limit' => 'danger',
        ];
    }
}
