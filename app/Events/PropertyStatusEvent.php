<?php

namespace App\Events;

use App\Models\Property;

class PropertyStatusEvent extends Event
{
    public $company, $property;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Property $property, $company)
    {
        // dd($property);
        $this->_company = $company;
        $this->_property = $property;
    }
}
