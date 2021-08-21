<?php

namespace App\Utils;

class MyAppEnv
{
    const MARLA = 1;
    const KANAL = 2;

    public static $types = [
        self::MARLA => 'marla',
        self::KANAL => 'kanal',
    ];
}
