<?php

namespace App\Core\Utils;

class Debug
{

    private static $entries = [];

    public static function add($smth)
    {
        self::$entries[] = $smth;
    }

    public static function print()
    {
        var_dump(... static::$entries);
    }

}