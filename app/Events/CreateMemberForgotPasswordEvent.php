<?php

namespace App\Events;

use App\Models\User;

class CreateMemberForgotPasswordEvent extends Event
{
    public $customer;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->_user  = $user;
    }
}
