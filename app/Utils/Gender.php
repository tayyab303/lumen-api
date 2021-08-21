<?php
namespace App\Utils;

class Gender
{
    const MALE = 1;
    const FEMALE = 2;

    public static $types = [
        self::MALE =>'Male',
        self::FEMALE => 'Female',
    ];
}

