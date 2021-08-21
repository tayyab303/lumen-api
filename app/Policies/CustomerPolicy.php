<?php

namespace App\Policies;

use App\Utils\UserType;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    // use HandlesAuthorization;
    /**
    * Determine whether the user can update the model.
    *
    * @param \App\Models\User $user
    * @param \App\Models\=Customer $customer
    * @return mixed
    */
    public function update(User $user, Customer $customer)
    {
        if($user->id === $customer->user_id){
            return true;
        }
        else{
            return false;
        }
    }

    /**
    * Determine whether the user can view any models.
    *
    * @param \App\Models\User $user
    * @param \App\Models\Customer $customer
    * @return mixed
    */
    public function view(User $user, Customer $customer)
    {
        //
        if($user->id === $customer->user_id){
            return true;
        }
        else{
            return false;
        }
    }

    /**
    * Determine whether the user can delete the model.
    *
    * @param \App\Models\User $user
    * @param \App\Models\=Customer $customer
    * @return mixed
    */
    public function delete(User $user, Customer $customer)
    {
        //
        if($user->id === $customer->user_id || $user->type === UserType::SUPER_ADMIN){
            return true; // if admin allows the customer to delete itself in future return true
        }
        else{
            return false;
        }
    }
}