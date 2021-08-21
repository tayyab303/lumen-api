<?php

namespace App\Http\Controllers;

use App\Utils\AppConst;
use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeesResource;




class EmployeeController extends Controller
{
    /**
     * private array variable for validation
     * 
     * @return array
     */
    private $_validationRule = [
        /**
         * user validation
         */
        'first_name'=> 'required|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'last_name' => 'required|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'username' => 'required|min:5|regex:/^\S*$/u|unique:users',
        'email' => 'required|email|unique:users',
        'ph'=> 'required|min:9|max:15|regex:/[0-9]{9}/|unique:users',
        'cnic'=> 'required|regex:/[0-9]{9}/|min:9|max:13|unique:users',
        'zip_code'=> 'regex:/^(?!00000)(?<zip>(?<zip5>\d{5})(?:[ -](?=\d))?(?<zip4>\d{4})?)$/|min:5|max:12',
        'gender' => 'numeric',

        /**
         * Employee validation
         */
        'joining_date' => 'required|date|date_format:Y-m-d',
        'joining_salary' => 'required|regex:/^\d+(\.\d{1,2})?$/',

    ];

    /**
     * private array variable for custom messages
     * 
     * @return array
     */
    private $_customMessages = [

        //User Details validation custom messages
        'first_name.required' => 'Please enter First Name its required.',
        'first_name.min' => 'First Name of User must be at least 3 characters.',
        'first_name.max' => 'First Name of User shouldnot be greater than 20 characters.',
        'first_name.regex' => 'First Name format is invalid it should be alphabetic.',

        'last_name.required' => 'Please enter Last Name its required.',
        'last_name.min' => 'Last Name of User must be at least 3 characters.',
        'last_name.max' => 'Last Name of User shouldnot be greater than 20 characters.',
        'last_name.regex' => 'Last Name format is invalid it should be alphabetic.',

        'email.required' => "Please enter Employee Email its required.",
        'email.unique' => "This Email has already been linked with another account.",
        'email.email' => "Email format is invalid it should be like i.e jhon@don.com.",

        'username.required' => 'Please enter Username its required.',
        'username.unique' => "This Username has already been taken please chose another.",
        'username.min' => 'Username of User must be at least 5 characters.',
        'username.regex' => 'Username format is invalid it should be like i.e jhon@123, jhon123 .',

        'ph.required' => 'Please enter Phone number its required.',
        'ph.unique' => 'Entered phone number has already been linked with another account.',
        'ph.min' => 'Phone Number must be at least 9 digits.',
        'ph.max' => 'Phone Number shouldnot be greater than 15 digits.',
        'ph.regex' => 'The phone number format is invalid it should be digits.',

        'cnic.required' => 'Please enter CNIC number its required.',
        'cnic.unique' => "This CNIC number has already been linked with another account.",
        'cnic.min' => 'CNIC of User must be at least 9 characters.',
        'cnic.max' => 'CNIC of User shouldnot be greater than 13 characters.',
        'cnic.regex' => 'CNIC format is invalid it should be numeric.',
        
        'zip_code.min' => 'Zip Code must be at least 5 digits.',
        'zip_code.max' => 'Zip Code shouldnot be greater than 12 characters.',
        'zip_code.regex' => 'Zip Code format is invalid it should be like i.e 54000, 01000-0000 .',

        //Employee Details validation custom messages
        'joining_date.required' => 'Please enter Employee Joining Date its required.',
        'joining_date.date' => 'Joining Date format is invalid it should be like i.e 2021-05-25.',
        'joining_salary.required' => 'Please enter Employee Joining Salary its required.',
        'joining_salary.regex' => 'Employee Joining Salary field format is invalid it should be numeric.',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Employee $employee)
    {
        $this->_employee = $employee;
    }

    /**
     * show list of Employees
     * 
     * @param Request $request
     * @return json response
     */
    public function  index(Request $request){
        $search = $this->validate($request, [
            'search' => ['regex:/^[\w]/']
        ]);
        $search = [
            'search' => $request->search, 'city' => $request->city, 'salary' => $request->salary,
        ];
        try {
            if(Auth::user()->can('viewAny',  $this->_employee)){
                $result = $this->_employee->searchEmployees($search);
                if(count($result)>0){
                    return new EmployeesResource($result);
                }else{
                    return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
                }
            }else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
        } catch (\Exception $e) {
            Log::error('EmployeeController -> index: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store Employee function
     *
     * @param Request $request
     * @return json response
     */
    public function store(Request $request)
    {
        /**
         * incoming Validate
         */
        if(Auth::user()->can('create',  $this->_employee)){
            $this->validate($request, $this->_validationRule, $this->_customMessages);
        }else {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
        }
        try {
            $employee=$this->_employee->createEmployee($request);
            return response()->json(['entity' => $employee, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]],HttpStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('EmployeeController -> store: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Add Profile Photo for employee
     * 
     * @param Request $request 
     * @param integer $id
     * 
     * @return image photo
     */
    public function employeeProfileImage(Request $request, $id)
    {
        $this->validate($request, [
            'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        try {
            $employee=$this->_employee->employeeImage($request, $id);
            return response()->json(['message' => 'Profile Photo  '. HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]],HttpStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('EmployeeController -> employeeProfileImage: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display a specified Employee.
     *
     * @param  int  $id
     * @return Response json
     */
    public function show($id){
        try {
            $employee = $this->_employee->with('user')->has('user')->find($id);
            if($employee != null){
                if(Auth::user()->can('view', $employee)){
                    return new EmployeeResource($employee);
                }else {
                    return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
                }
            }
            else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        }
        catch (\Exception $e) {
            Log::error('EmployeeController -> show: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Show the form for editing the specified Employee.
     *
     * @param  int  $id
     * @return Response json
     */
    public function edit($id){
        try {
            $employee = $this->_employee->with('user')->has('user')->find($id);
        } catch (\Exception $e) {
            Log::error('EmployeeController -> edit: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }        
        if($employee != null){
            if(Auth::user()->can('update', $employee)){
                return response()->json(['entity' => $employee, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
        }else{
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
    }

    /**
     * Update the specified Employee resource in storage.
     *
     * @param int $id
     * @param Request $request 
     * @return Response json
     */
    public function update(Request $request, $id)
    {
        $employee = $this->_employee->with('user')->has('user')->find($id);
        if($employee == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        
        $filtered = Arr::except($this->_validationRule, [
            'email', 'username', 'ph', 'cnic', 'password'
        ]);
            
        $filtered += ([
                /**
                 * user validation rule
                 */
            'ph'=> 'string|min:9|max:15|regex:/[0-9]{9}/|unique:users,ph,'.$employee->user->id,
            'username' => 'min:5|regex:/^\S*$/u|unique:users,username,'.$employee->user->id,
            'cnic' => 'min:9|max:13|regex:/[0-9]{9}/|unique:users,cnic,'.$employee->user->id,
            'email' => 'email|unique:users,email,'.$employee->user->id,
            'password' => 'min:8|max:64'.$employee->user->id,
        ]);
            
        /**
         * validate incoming data
         */
        if(Auth::user()->can('update',  $employee)){
            $this->validate($request, $filtered, $this->_customMessages);
        } else {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
        }
            
        try {
            $employee=$this->_employee->updateEmployee($request,$id);
            return response()->json(['entity' => $employee, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        } catch (\Exception $e) {
            //Log::error('EmployeeController -> update: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified Employee from storage.
     *
     * @param  int  $id
     * @return Response json
     */
    public function destroy($id){
        $employee = $this->_employee->with('user')->find($id);
        if(Auth::user()->can('delete',  $employee)){
            if($employee == null){
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        } else {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
        }
        try {
            $image = $employee->photo;
            if(file_exists($image)) {
                unlink($image);
            }
            $employee->user()->delete();
            $employee->delete();
            return response()->json(['entity' => $employee, 'message' => 'Deleted! '.HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('EmployeeController -> destroy: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
