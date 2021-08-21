<?php

namespace App\Events;

use App\Models\Company;

class CreateCompanyEvent extends Event
{
    public $company , $password;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Company $company, $password)
    {
      // dd($company,$password);
        $this->_company  = $company;
        $this->_password  = $password;

    }
}
