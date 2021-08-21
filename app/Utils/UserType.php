<?php

namespace App\Utils;

class UserType{
    const SUPER_ADMIN=1;
    const COMPANY=2;
    const CUSTOMER=3;
    const SUPER_EMPLOYEE=4;
    const COMPANY_EMPLOYEE=5;

    public static $types = [
        self::SUPER_ADMIN => 'Super Admin',
        self::COMPANY => 'Company',
        self::CUSTOMER => 'Customer',
        self::SUPER_EMPLOYEE => 'Super Employee',
        self::COMPANY_EMPLOYEE => 'Company Employee',
    ];
}
