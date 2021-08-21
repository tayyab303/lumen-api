<?php

namespace App\Policies;

use App\Models\User;
use App\Utils\UserType;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    // use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    
    public function viewAny(?User $user, Employee $employee)
    {
        if($user->type === UserType::SUPER_ADMIN || UserType::COMPANY){
            return true;
        }
        else{
            return false;
        }
    }
    
    /**
    * Determine whether the user can create the model.
    *
    * @param \App\Models\Employee $employee
    * @param \App\Models\=User $user
    * @return mixed
    */
    public function create(?User $user, Employee $employee)
    {
        if($user->type === UserType::SUPER_ADMIN || UserType::COMPANY){
            return true;
        }
        else{
            return false;
        }
    }

    /**
    * Determine whether the user can update the model.
    *
    * @param \App\Models\Employee $employee
    * @param \App\Models\=User $user
    * @return mixed
    */
    public function update(?User $user, Employee $employee)
    {
        if(
            ($user->type === UserType::SUPER_ADMIN && $employee->company === null)
            || 
            ($user->type === UserType::COMPANY && $employee->company !== null && $user->company->id === $employee->company->id)
        ){
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
    * @param \App\Models\Employee $employee
    * @return mixed
    */
    public function view(?User $user, Employee $employee)
    {
        if(
            ($user->type === UserType::SUPER_ADMIN )
            || 
            ($user->type === UserType::COMPANY && $employee->company !== null && $user->company->id === $employee->company->id)
        ){
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
    * @param \App\Models\=Employee $employee
    * @return mixed
    */
    public function delete(?User $user, Employee $employee)
    {
        if(
            ($user->type === UserType::SUPER_ADMIN && $employee->user->type === UserType::SUPER_EMPLOYEE || UserType::COMPANY_EMPLOYEE)
            || 
            (($user->type === UserType::COMPANY && $employee->user->type === UserType::COMPANY_EMPLOYEE))
        ){
            return true;
        }
        else{
            return false;
        }
    }
}