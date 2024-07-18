<?php

namespace Webard\NovaZadarma\Traits;

trait EnumBadgeTrait
{
    abstract public static function map(): array;

    public static function labels(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'name')
        );
    }

    public static function icons(): array
    {
        return [];
    }
}
