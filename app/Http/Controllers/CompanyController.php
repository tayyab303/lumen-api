<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;
use App\Http\Resources\CompaniesResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CompanyResource;
use Illuminate\Support\Facades\Log;


class CompanyController extends Controller
{
    /**
     * validation rules variable
     *  
     * @var array
     */
    private $_validationRules = [
        /**
         * user validation
         */
        'first_name'=> 'required|string|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'last_name' => 'required|string|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'ph'=> 'required|min:9|max:15|regex:/[0-9]{9}/|unique:users',   // allow only phone
        'username' => 'required|min:5|regex:/^\S*$/u|unique:users',
        'email' => 'required|email|unique:users',     // allow only email
        'cnic'=> 'min:9|max:13|regex:/[0-9]{9}/|unique:users', 
        'address' => 'min:15|max:150',
        'city' => 'regex:/^[\pL\s\-]+$/u',                    // allow only name alphabetical character
        'state' => 'regex:/^[\pL\s\-]+$/u',                   // allow only name alphabetical character
        'country' => 'regex:/^[\pL\s\-]+$/u',                   // allow only name alphabetical character
        'zip_code'=> 'regex:/^(?!00000)(?<zip>(?<zip5>\d{5})(?:[ -](?=\d))?(?<zip4>\d{4})?)$/|min:5|max:12',
        'gender' => 'numeric',

        /**
         * company validation
         */
        'name' => 'required|regex:/^[a-zA-Z_ ]*$/|min:5|max:80|unique:companies',        // allow only alphabetical character
        'company_email' => 'required|email|unique:companies,email',     // allow only email
        'company_ph'=> 'required|min:9|max:15|regex:/[0-9]{9}/|unique:companies,ph',   // allow only phone
        'fax'=> 'min:9|max:15|regex:/^\+?[0-9]{7,}$/',   // allow only number
        'company_address' => 'min:15|max:150',                  
        'company_city' => 'regex:/^[\pL\s\-]+$/u',                    // allow only name alphabetical character
        'company_state' => 'regex:/^[\pL\s\-]+$/u',                   // allow only name alphabetical character
        'company_country' => 'regex:/^[\pL\s\-]+$/u',                   // allow only name alphabetical character
        'company_zip_code'=> 'regex:/^(?!00000)(?<zip>(?<zip5>\d{5})(?:[ -](?=\d))?(?<zip4>\d{4})?)$/|min:5|max:12',
        'about' => 'min:50|max:1000',
        'location' => 'string|regex:/^[\pL\s\-]+$/u|min:2|max:100'
    ];
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

        //Company Details Validation custom messages
        'name.required' => "Please enter Company name its required.",
        'name.unique' => "This Company name has already register please chose another.",
        'name.min' => "Company name must be at least 5 characters.",
        'name.max' => "Company name shouldnot be greater than 80 characters.",
        'name.regex' => "Company name format is invalid it should be alphabetical like i.e Zameen Real Estate.",

        'company_email.required' => "Please enter Company Email its required.",
        'company_email.unique' => "This Email has already been linked with another account.",
        'company_email.email' => "Email format is invalid it should be like i.e realestate@company.com.",

        'company_ph.required' => "Please enter Company contact number its required.",
        'company_ph.unique' => "Entered Company phone number has already been linked with another account.",
        'company_ph.min' => "Company phone number must be at least 9 digits.",
        'company_ph.max' => "Company phone Number shouldnot be greater than 15 digits.",
        'company_ph.regex' => "The Company phone number format is invalid it should be digits.",

        'fax.min' => "Company fax number must be at least 9 digits.",
        'fax.max' => "Company fax Number shouldnot be greater than 15 digits.",
        'fax.regex' => "The Company fax number format is invalid it should be digits.",

        'company_zip_code.min' => 'Company Zip Code must be at least 5 digits.',
        'company_zip_code.max' => 'Company Zip Code shouldnot be greater than 12 characters.',
        'company_zip_code.regex' => 'Company Zip Code format is invalid it should be like i.e 54000, 01000-0000 .',
        
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Company $company)
    {
        $this->_company = $company;
    }

    /**
     * show list of companies
     * @return json response
     */
    public function index(Request $request){
        $search = $this->validate($request, [
            'search' => ['regex:/^[\w]/']
        ]);
        try {
            $search = [
                'search' => $request->search, 'status' => $request->status, 'city' => $request->city, 'state' => $request->state,
                'sorting' => ['sort' => $request->sort, 'column' => $request->column]
            ];
            $result = $this->_company->searchCompanies($search);
            if(count($result)>0){
                return new CompaniesResource($result);
            }else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('CompanyController -> index: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * store company function
     *
     * @param Request $request
     * @return json response
     */
    public function store(Request $request){
        /**
         * incoming Validate users table
         */
        $this->validate($request, $this->_validationRules, $this->_customMessages);
        $company = $this->_company->createCompany($request);
        try{
            return response()->json(['entity' => $company, 'message' =>  HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]],HttpStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('CompanyController -> store: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Add logo for company
     * 
     * @param Request $request 
     * @param integer $id
     * 
     * @return image photo
     */
    public function companyLogoImage(Request $request, $id)
    {
        $this->validate($request, [
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            try {
                $company = $this->_company->with('user')->has('user')->find($id);
                if($company && Auth::user()->can('view', $company)){
                    $company=$this->_company->companyLogo($request, $id);
                    return response()->json(['message' => "Company's Logo   " .  HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]],HttpStatusCode::CREATED);
                } else{
                    return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
                }
            } catch (\Exception $e) {
            Log::error('CompanyController -> companyLogoImage: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified company.
     *
     * @param  int  $id
     * @return Response json
     */
    public function show($id){
        try {
            if($this->_company->with('user')->has('user')->find($id) != null){
                return new CompanyResource($this->_company->with('user')->has('user')->with('properties')->find($id));
            }else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('CompanyController -> show: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified company.
     *
     * @param  int  $id
     * @return Response json
     */
    public function edit($id){
        $company = $this->_company->with('user')->has('user')->find($id);
        if($company == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        try {
            if($company && Auth::user()->can('view', $company)){
                return response()->json(['entity' => $company, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
        } catch (\Exception $e) {
            Log::error('CompanyController -> edit: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param Request $request
     * @return Response json
     */
    public function update(Request $request , $id){
        
        $company = $this->_company->find($id);
        if($company == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        //validate incoming request
        $filtered = Arr::except($this->_validationRules, [
            'name', 'company_email','company_ph',
            'email','username','ph','cnic'
        ]);
        $filtered += ([
            /**
             * company validation rule
             */
            'name'=>'required|regex:/^[\pL\s\-]+$/u|unique:companies,name,'.$id,
            'company_ph'=> 'required|string|min:9|max:15|regex:/[0-9]{9}/|unique:companies,ph,'.$id,
            'company_email' => 'required|email|unique:companies,email,'.$id,

            /**
             * user validation rule
             */
            'username' => 'min:5|regex:/^\S*$/u|unique:users,username,'.$company->user->id,
            'ph'=> 'string|min:9|max:15|regex:/[0-9]{9}/|unique:users,ph,'.$company->user->id,
            'email' => 'email|unique:users,email,'.$company->user->id,
            'cnic' => 'min:9|max:13|regex:/[0-9]{9}/|unique:users,cnic,'.$company->user->id,
        ]);

        /**
         * validate incoming data
         */
        $this->validate($request,$filtered, $this->_customMessages);

        try {
            if($company && Auth::user()->can('update', $company))
            {
                $company = $this->_company->updateCompany($request,$id);
            }else{
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
            return response()->json(['entity' => $company, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::ACCEPTED]], HttpStatusCode::ACCEPTED);
        } catch (\Exception $e) {
            Log::error('CompanyController -> update: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response json
     */
    public function destroy($id){
        $company = $this->_company->find($id);
        if($company == null){
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        try {
            if($company && Auth::user()->can('delete', $company))
            {
                if($company->logo != null) {
                unlink($company->logo);
            }
            $company->user()->delete();
            $company->delete();
            return response()->json(['entity' => new CompanyResource($company), 'message' => 'Deleted! '.HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
        } catch (\Exception $e) {
            Log::error('CompanyController -> destroy: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Search company
     *
     * @param Request $request
     * @return json
     */
    public function search(Request $request)
    {
        $search=$this->validate($request,[
            'search'=>['regex:/^[\w]/']
        ]);
        $search=['search'=>$request->search,'status'=>$request->status,'city'=>$request->city,'state'=>$request->state,
        'sorting'=>['sort'=>$request->sort,'column'=>$request->column]];
        try{
            $result=$this->_company->searchCompanies($search);
            return new CompaniesResource($result);
        }catch (\Exception $e)
        {
            Log::error('CompanyController -> seach: ',$e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
}
