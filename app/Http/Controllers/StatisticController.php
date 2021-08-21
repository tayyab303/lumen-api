<?php


namespace App\Http\Controllers;

use App\Utils\HttpStatusCode;
use App\Models\User;
use App\Models\Company;
use App\Models\Property;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Utils\PropertyStatus;
use App\Utils\UserType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StatisticController  extends  Controller
{
    /**
     * Create a new controller instance.
     * @var App\Models\Statistic $_statistic
     * @return void
     */
    public function __construct(Property $property, User $user, Category $category, Company $company, Customer $customer, Employee $employee)
    {
        $this->_property = $property;
        $this->_company = $company;
        $this->_customer = $customer;
        $this->_employee = $employee;
        $this->_category = $category;
        $this->_user = $user;
    }
    /**
     * stats for admin penal
     *
     * @param Request $request
     * @return void
     */
    public function adminStats(Request $request)
    {
        try {
            if (Auth::user()->type === UserType::SUPER_ADMIN) {
                $property =  $this->_property->count(); // total properties
                $company =  $this->_company->count();  // total compannies
                $customer =  $this->_customer->count(); // total customers
                $employee =  $this->_employee->count(); // total employees
                $category =  $this->_category->count(); // total categories
                $user =  $this->_user->count();  // total users
                // total available properties
                $available_property = $this->_property->where('is_available', '=', PropertyStatus::AVAILABLE)->count();
                // total sold properties
                $sold_property = $this->_property->where('is_available', '=', PropertyStatus::SOLD)->count();
                // total approved properties
                $verified_property = $this->_property->where('status', '=', PropertyStatus::APPROVED)->count();
                // total rejected properties
                $unverified_property = $this->_property->where('status', '=', PropertyStatus::REJECTED)->count();
                // total pending properties
                $pending_property = $this->_property->where('status', '=', PropertyStatus::PENDING)->count();
                // total assigned properties
                $assigned_property = $this->_property->where('employee_id', '!=', null)->count();
                // total others companies properties
                $property_by_company = $this->_property->where('company_id', '!=', null)->count();
                // total properties by admin
                $property_by_admin = $this->_property->where('company_id', '=', null)->count();
                // total employees by admin
                $employee_by_admin = $this->_employee->where('company_id', '=', null)->count();
                // to empployees by company
                $employee_by_company = $this->_employee->where('company_id', '!=', null)->count();
                // an array of all stats
                $stats = [
                    "properties" => $property,
                    "companies" => $company,
                    "customers" => $customer,
                    "employees" => $employee,
                    "categories" => $category,
                    "users" => $user,

                    "available_properties" => $available_property,
                    "sold_properties" => $sold_property,
                    "verified_properties" => $verified_property,
                    "unverified_properties" => $unverified_property,

                    "pending_properties" => $pending_property,
                    "assigned_properties" => $assigned_property,
                    "property_by_company" => $property_by_company,
                    "property_by_admin" => $property_by_admin,
                    "employee_by_admin" => $employee_by_admin,
                    "employee_by_company" => $employee_by_company,
                    // properties by cities
                    "lahore" => $this->_property->where('city', '=', "Lahore" )->count(),
                    "islamabad" => $this->_property->where('city', '=', "Islamabad" )->count(),
                    "karachi" => $this->_property->where('city', '=', "Karachi" )->count(),
                    "peshawer" => $this->_property->where('city', '=', "Peshawer" )->count(),
                    "quetta" => $this->_property->where('city', '=', "Quetta" )->count(),
                    "gujranwala" => $this->_property->where('city', '=', "Gujranwala" )->count(),
                    "companies_by_lahore" => $this->_company->where('city', '=', "Lahore" )->count(),
                    "companies_by_islamabad" => $this->_company->where('city', '=', "Islamabad" )->count(),
                    "companies_by_karachi" => $this->_company->where('city', '=', "karachi" )->count(),
                    "companies_by_peshawer" => $this->_company->where('city', '=', "Peshawer" )->count(),
                    "companies_by_quetta" => $this->_company->where('city', '=', "Quetta" )->count(),
                    "companies_by_gujranwala" => $this->_company->where('city', '=', "Gujranwala" )->count(),
                ];
                return response()->json(['statistics' => $stats, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }
            if (Auth::user()->type === UserType::COMPANY) {
                $id = Auth::user()->company_id;
                // emmployees bby company relevant to this  $id
                $employees = $this->_employee->where('company_id', '=', $id)->count();
                // total properties by company relevant to this  $id
                $property_by_company = $this->_property->where('company_id','=',$id)->count();
                // total  available properties by company relevant to this  $id
                $available_properties = $this->_property->where([['company_id', '=', $id], ['is_available', '=', PropertyStatus::AVAILABLE]])->count();
                // total sold properties by company relevant to this  $id
                $sold_properties = $this->_property->where([['company_id', '=', $id], ['is_available', '=', PropertyStatus::SOLD]])->count();
                // total pending properties by company relevant to this  $id
                $pending_properties = $this->_property->where([['company_id', '=', $id], ['status', '=', PropertyStatus::PENDING]])->count();
                // total assigned properties by company relevant to this  $id
                $assigned_properties = $this->_property->where([['company_id', '=', $id], ['employee_id', '!=', null]])->count();
                // total approved properties by company relevant to this  $id
                $verified_properties = $this->_property->where([['company_id', '=', $id], ['status', '=', PropertyStatus::VERIFIED]])->count();
                // total rejected  properties by company relevant to this  $id
                $unverified_properties = $this->_property->where([['company_id', '=', $id], ['status', '=', PropertyStatus::UNVERIFIED]])->count();


                $stats = [
                    "available_properties" => $available_properties,
                    "property_by_company" => $property_by_company,
                    "employees" => $employees,
                    "sold_properties" => $sold_properties,
                    "pending_properties" => $pending_properties,
                    "assigned_properties" => $assigned_properties,
                    "verified_properties" => $verified_properties,
                    "unverified_properties" => $unverified_properties,
                    // total properties againnst cities
                    "lahore" => $this->_property->where('city', '=', "Lahore" )->where('company_id', '=', $id)->count(),
                    "islamabad" => $this->_property->where('city', '=', "Islamabad" )->where('company_id', '=', $id)->count(),
                    "karachi" => $this->_property->where('city', '=', "Karachi" )->where('company_id', '=', $id)->count(),
                    "peshawer" => $this->_property->where('city', '=', "Peshawer" )->where('company_id', '=', $id)->count(),
                    "quetta" => $this->_property->where('city', '=', "Quetta" )->where('company_id', '=', $id)->count(),
                    "gujranwala" => $this->_property->where('city', '=', "Gujranwala" )->where('company_id', '=', $id)->count(),

                ];
                return response()->json(['statistics' => $stats, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        }
        if (Auth::user()->type === UserType::SUPER_EMPLOYEE || Auth::user()->type === UserType::COMPANY_EMPLOYEE) {
                $id = Auth::user()->employee->id;
                // total assigned properties by employee relevant to this  $id
                $employee_assigned_properties = $this->_property->where('employee_id', '=', $id)->count();
                // total assigned approved properties by employee relevant to this  $id
                $employee_approved_properties = $this->_property->where('employee_id', '=', $id)->where('status', '=', PropertyStatus::APPROVED)->count();
                // total assigned rejected properties by employee relevant to this  $id
                $employee_rejected_properties = $this->_property->where('employee_id', '=', $id)->where('status', '=', PropertyStatus::REJECTED)->count();
                // total assigned pending properties by employee relevant to this  $id
                $employee_pending_properties = $this->_property->where('employee_id', '=', $id)->where('status', '=', PropertyStatus::PENDING)->count();
                // total assigned available properties by employee relevant to this  $id
                $available_property = $this->_property->where('is_available', '=', PropertyStatus::AVAILABLE)->where('employee_id', '=', $id)->count();
                // total assigned sold properties by employee relevant to this  $id
                $sold_property = $this->_property->where('is_available', '=', PropertyStatus::SOLD)->where('employee_id', '=', $id)->count();
                $stats = [
                    "employee_assigned_properties" => $employee_assigned_properties,
                    "employee_approved_properties" => $employee_approved_properties,
                    "employee_rejected_properties" => $employee_rejected_properties,
                    "employee_pending_properties" => $employee_pending_properties,
                    "available_properties" => $available_property,
                    "sold_properties" => $sold_property,
                    // total assigned properties against cities
                    "lahore" => $this->_property->where('city', '=', "Lahore" )->where('employee_id', '=', $id)->count(),
                    "islamabad" => $this->_property->where('city', '=', "Islamabad" )->where('employee_id', '=', $id)->count(),
                    "karachi" => $this->_property->where('city', '=', "Karachi" )->where('employee_id', '=', $id)->count(),
                    "peshawer" => $this->_property->where('city', '=', "Peshawer" )->where('employee_id', '=', $id)->count(),
                    "quetta" => $this->_property->where('city', '=', "Quetta" )->where('employee_id', '=', $id)->count(),
                    "gujranwala" => $this->_property->where('city', '=', "Gujranwala" )->where('employee_id', '=', $id)->count(),
                ];
                return response()->json(['statistics' => $stats, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        Log::error('StatisticsController -> stats: ', $e);
            }
    }

    /**
     * public stats function
     *
     * @return void
     */
    public function stats()
    {
        try {
            $stats = [
                "properties" => $this->_property->count(),
                "companies" => $this->_company->count(),
                "emplyees" => $this->_employee->count(),
                "categories" => $this->_category->count(),

                "lahore" => $this->_property->where('city', '=', "lahore" )->count(),
                "islamabad" => $this->_property->where('city', '=', "islamabad" )->count(),
                "karachi" => $this->_property->where('city', '=', "karachi" )->count(),
                "peshawer" => $this->_property->where('city', '=', "peshawer" )->count(),
                "quetta" => $this->_property->where('city', '=', "quetta" )->count(),
                "gujranwala" => $this->_property->where('city', '=', "gujranwala" )->count(),
            ];
            return response()->json(['statistics' => $stats, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('StatisticsController -> stats: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
