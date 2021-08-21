<?php

namespace App\Utils;

class EmployeeType{
    const ADMIN=1;
    const MANAGER=2;
    const SURVEY_INSPECTOR=3;
    const OPERATOR=4;

    public static $types = [
        self::ADMIN => 'Admin',
        self::MANAGER => 'Manager',
        self::SURVEY_INSPECTOR => 'Survey Inspector',
        self::OPERATOR => 'Operator',
    ];
}
