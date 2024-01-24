<?php

namespace App\Traits;


trait EnumUtils
{

    static function getKeys(): array
    {
        return array_column(self::cases(), 'name');
    }

    static function getValues($includeUndefined = true): array
    {
        
        $values = array_column(self::cases(), 'value');
        if (!$includeUndefined)
            $values = array_filter($values, function ($value) {
                return $value != 'undefined';
            });
        return $values;
    }

    static function getValue($key): int|string|null
    {
        $foundEnum = array_filter(
            self::cases(),
            fn ($item) => $item->name == $key
        );
        return count($foundEnum) > 0 ? current($foundEnum)->value : null;
    }
}
