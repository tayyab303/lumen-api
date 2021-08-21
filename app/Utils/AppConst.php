<?php

namespace App\Utils;

class AppConst
{
    const PAGE_SIZE = 100;

    const FEATURED_PROPERTIES = 6;

    const NO = 0;
    const YES = 1;

    const INACTIVE = 0;
    const ACTIVE = 1;

    const ASC= 'asc';
    const DESC= 'desc';

    const UNVERIFIED=0;
    const VERIFIED=1;

    const VERIFICATION_CODE = 6;
    
    const ONE_MONTH = 2629746;

    // algorithms
    const HASH_ALGO = 'sha256';
    const JWT_ALGO = 'HS256';
}
