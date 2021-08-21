<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Company;
use App\Utils\UserType;
use Illuminate\Auth\Access\HandlesAuthorization;


class CompanyPolicy
{
    // use HandlesAuthorization;
    /**
    * Determine whether the user can update the model.
    *
    * @param \App\Models\User $user
    * @param \App\Models\=Company $company
    * @return mixed
    */
    public function update(?User $user, Company $company)
    {
        //
        if( $user->type === UserType::SUPER_ADMIN || $user->company->id === $company->id ){
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
    * @param \App\Models\Company $company
    * @return mixed
    */
    public function view(?User $user, Company $company)
    {
        if( $user->type === UserType::SUPER_ADMIN || $user->company->id === $company->id ){
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
    * @param \App\Models\=Company $company
    * @return mixed
    */
    public function delete(?User $user, Company $company)
    {
        //
        if( $user->type === UserType::SUPER_ADMIN || $user->company->id === $company->id ){
            return true;
        }
        else{
            return false;
        }
    }
}