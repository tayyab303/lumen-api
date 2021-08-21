<?php

namespace App\Events;

use App\Models\Employee;
use App\Models\Property;

class AssignPropertyEvent extends Event
{
    public $employee, $property;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, Property $property)
    {
        $this->employee = $employee;
        $this->property = $property;
    }
}
