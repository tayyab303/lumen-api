<?php
namespace App\Utils;

class MyAppEnv
{
    const LOCAL = 'local';
    const DEVELOPMENT = 'development';
    const STAGING = 'staging';
    const PRODUCTION = 'production';

    public static $types = [
        self::LOCAL=>self::LOCAL,
        self::DEVELOPMENT=>self::DEVELOPMENT,
        self::STAGING=>self::STAGING,
        self::PRODUCTION=>self::PRODUCTION,
    ];
}
