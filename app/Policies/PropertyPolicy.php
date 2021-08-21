<?php

namespace App\Policies;

use App\Models\User;
use App\Utils\UserType;
use App\Models\Property;
use Illuminate\Auth\Access\HandlesAuthorization;

class PropertyPolicy
{
    // use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    
    // public function viewAny(?User $user, Property $property)
    // {
        //

    // }
    
    /**
    * Determine whether the user can viewemployee of  the model.
    *
    * @param \App\Models\Property $property
    * @param \App\Models\=User $user
    * @return mixed
    */
    public function view(?User $user, Property $property)
    {
        if(
            ($user->type === UserType::COMPANY && $user->company_id === $property->company_id  && $property->company_id !== null) 
                || 
            ($user->type === UserType::SUPER_ADMIN)){
            return true;
        }
        else{
            return false;
        }
    }

    /**
    * Determine whether the user can update the model.
    *
    * @param \App\Models\Property $property
    * @param \App\Models\=User $user
    * @return mixed
    */
    public function update(?User $user, Property $property)
    {
        if(
            ($user->type === UserType::COMPANY && $user->company->id === $property->company->id  && $property->company->id !== null) 
                || 
            ($user->type === UserType::SUPER_ADMIN && $property->company_id == null)){
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
    * @param \App\Models\Property $property
    * @return mixed
    */
    // public function view(?User $user, Property $property)
    // {
        //
        // if($property->company_id === $user->company_id && $property->company != null || $user->type === UserType::SUPER_ADMIN){
        //     return true;
        // }
        // else{
        //     return false;
        // }
    // }

    /**
    * Determine whether the user can delete the model.
    *
    * @param \App\Models\User $user
    * @param \App\Models\=Property $property
    * @return mixed
    */
    public function delete(?User $user, Property $property)
    {
        //
        if(  ($user->type === UserType::COMPANY && $user->company->id === $property->company->id  && $property->company->id !== null) || ($user->type === UserType::SUPER_ADMIN)){
            return true;
        }
        else{
            return false;
        }
    }
}