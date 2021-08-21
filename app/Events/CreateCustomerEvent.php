<?php

namespace App\Events;

use App\Models\Customer;

class CreateCustomerEvent extends Event
{
    public $customer;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
      // dd($customer);
        $this->_customer  = $customer;
    }
}
