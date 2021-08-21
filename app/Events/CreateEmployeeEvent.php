<?php

namespace App\Events;

use App\Models\Employee;

class CreateEmployeeEvent extends Event
{
    public $employee, $password;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, $password)
    {
        $this->_employee  = $employee;
        $this->_password  = $password;
    }
}
