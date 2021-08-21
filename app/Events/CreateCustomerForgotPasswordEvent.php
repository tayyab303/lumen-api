<?php

namespace App\Events;

use App\Models\Customer;

class CreateCustomerForgotPasswordEvent extends Event
{
    public $customer;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
        $this->_customer  = $customer;
    }
}
