<?php

namespace App\Http\Controllers;


use App\Utils\AppConst; 
use App\Models\Customer;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Utils\HttpStatusCode;   
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomersResource;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Payment;
use App\Utils\UserType;
use Illuminate\Support\Facades\Redirect;

use function Ramsey\Uuid\v1;

class CustomerController extends Controller
{

    /**
     * Validation variable
     *
     * @var array
     */
    private $_validationRules = [
        /**
         * user table validation
         */
        'first_name' => 'required|string|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'last_name' => 'required|string|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'ph' => 'string|min:9|max:15|regex:/[0-9]{9}/|unique:users',
        'username' => 'required|min:5|regex:/^\S*$/u|unique:users',
        'state' => 'regex:/^[\pL\s\-]+$/u|min:2|max:100',
        'city' => 'regex:/^[\pL\s\-]+$/u|min:2|max:100',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|max:64',
        'cnic' => 'string|min:9|max:13|regex:/[0-9]{9}/|unique:users',
        'zip_code' => 'numeric|min:5',
        'gender' => 'numeric',

        /**
         * customer table validation
         */
        'name' => 'string|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'bank_name' => 'string|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'account_title' => 'string|regex:/^[\pL\s\-]+$/u|min:3|max:20',
        'account_no' => 'regex:/[0-9]{9}/|min:8|max:20|unique:customers',
        'iban' => 'regex:/[0-9]{9}/|min:10|max:35|unique:customers',
        'is_overseas' => 'boolean'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Customer $customer, Payment $payment)
    {
        $this->_customer = $customer;
        $this->_payment = $payment;
    }

    /**
     * Show list of Customers 
     * 
     * @param Request $request
     * @return json response
     */
    public function index(Request $request)
    {
        $search = $this->validate($request, [
            'search' => ['regex:/^[\w]/']
        ]);
        try {
            $search = [
                'search' => $request->search, 'bank' => $request->bank, 'sorting' => ['sort' => $request->sort, 'column' => $request->column]
            ];
            $result = $this->_customer->searchCustomers($search);
            if (count($result) > 0) {
                return new CustomersResource($result);
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('CustomerController -> index: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * store Cutomer function
     *
     * @param Request $request
     * @return json response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->_validationRules);
        try {
            $customer = $this->_customer->createCustomer($request);
            return response()->json(['entity' => $customer, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]], HttpStatusCode::CREATED);
        } catch (\Exception $e) {
            Log::error('CustomerController -> store: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Add Profile Photo for customer
     * 
     * @param Request $request 
     * @param integer $id
     * 
     * @return image photo
     */
    public function customerProfileImage(Request $request, $id)
    {
        $this->validate($request, [
            'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $customer = $this->_customer->with('user')->has('user')->find($id);
        try {
            if ($customer && Auth::user()->can('view', $customer)) {
                $customer = $this->_customer->customerImage($request, $id);
                return response()->json(['message' => 'Profile Photo  ' .  HttpStatusCode::$statusTexts[HttpStatusCode::CREATED]], HttpStatusCode::CREATED);
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
        } catch (\Exception $e) {
            Log::error('CustomerController -> CustomerProfileImage: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified Customer.
     *
     * @param  int  $id
     * @return Response json
     */
    public function show($id)
    {
        try {
            if ($this->_customer->with('user')->has('user')->find($id) != null) {
                return new CustomerResource($this->_customer->with('user')->has('user')->find($id));
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            Log::error('CustomerController -> show: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified Customer.
     *
     * @param  int  $id
     * @return Response json
     */
    public function edit($id)
    {
        $customer = $this->_customer->with('user')->find($id);
        if ($customer == null) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        try {

            if ($customer && Auth::user()->can('view', $customer)) {
                return response()->json(['entity' => new CustomerResource($customer), 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::FOUND]], HttpStatusCode::FOUND);
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
        } catch (\Exception $e) {
            Log::error('CustomerController -> edit: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response json
     */
    public function update(Request $request, $id)
    {
        $customer = $this->_customer->with('user')->has('user')->find($id);
        if ($customer == null) {

            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        $filtered = Arr::except($this->_validationRules, ['account_no', 'cnic', 'iban', 'username', 'ph', 'email', 'password']);
        $filtered += ([
            /**
             * customer table validation rule
             */
            'account_no' => 'regex:/[0-9]{9}/|min:8|max:20|unique:customers,account_no,' . $id,
            'iban' => 'regex:/[0-9]{9}/|min:10|max:35|unique:customers,iban,' . $id,
            /**
             * user table validation rule
             */
            'ph' => 'string|min:9|max:15|regex:/[0-9]{9}/|unique:users,ph,' . $customer->user->id,
            'username' => 'min:5|regex:/^\S*$/u|unique:users,username,' . $customer->user->id,
            'cnic' => 'min:9|max:13|regex:/[0-9]{9}/|unique:users,cnic,' . $customer->user->id,
            'email' => 'email|unique:users,email,' . $customer->user->id,
            'password' => 'min:8|max:64' . $customer->user->id,
        ]);
        $this->validate($request, $filtered);
        try {
            if ($customer && Auth::user()->can('update', $customer)) {
                $customer = $this->_customer->updateCustomer($request, $id);
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
            return response()->json(['entity' => $customer, 'message' => HttpStatusCode::$statusTexts[HttpStatusCode::ACCEPTED]], HttpStatusCode::ACCEPTED);
        } catch (\Exception $e) {
            Log::error('CustomerController -> update: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response json
     */
    public function destroy($id)
    {
        $customer = $this->_customer->find($id);
        if ($customer == null) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
        }
        try {
            if ($customer && Auth::user()->can('delete', $customer)) {
                $image = $customer->photo;
                if (file_exists($image)) {
                    unlink($image);
                }
                $user = $customer->user->only(['username', 'email']);
                $customer->user->delete();
                $customer->delete();
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FORBIDDEN]], HttpStatusCode::FORBIDDEN);
            }
            return response()->json(['entity' => $user, 'message' => 'Deleted! ' . HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
        } catch (\Exception $e) {
            Log::error('CustomerController -> destroy: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Verify Customer function
     *
     * @param User int $id
     * @param User verification int $code
     * @return void
     */
    public function verify(Request $request)
    {
        try {
            if (strlen($request->code) === AppConst::VERIFICATION_CODE) {
                $result = $this->_customer->verifyCustomer($request);
                return response()->json(['message' => $result . " " . HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            } else {
                return response()->json(['message' => "you have passed incorrect url. " . HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }
        } catch (\Exception $e) {
            Log::error('CustomerController -> show: ', $e);
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * forgot password function
     *
     * @param Request $request
     * @return void
     */
    public function forgotPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string',
        ]);

        try {
            $email = $request->get('email');
            // Get user with credentials
            $user = User::where('email', $email)->where('type', UserType::CUSTOMER)->first();
            if ($user) {
                $check = User::where('email', $email)->where('is_verified', AppConst::VERIFIED)->first();

                if ($check) {

                    $res = $this->_customer->resetPasswordMail($email);

                    return response()->json(['data' => $res,  'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
                } else {
                    return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_VERIFIED]], HttpStatusCode::NOT_VERIFIED);
                }
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::NOT_FOUND]], HttpStatusCode::NOT_FOUND);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * restore password function
     *
     * @param Request $request
     * @return void
     */
    public function restorePassword(Request $request)
    {
        try {
            $token = $request->get('token');

            // Get user with credentials
            $user = User::where('token', $token)->first();
            if ($user) {

                return view('customer.update-password', ['token' => $token]);
                // return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::FOUND]], HttpStatusCode::FOUND);
            } else {
                return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::UNAUTHORIZED]], HttpStatusCode::UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * update password function
     *
     * @param Request $request
     * @return void
     */
    public function updatePassword(Request $request)
    {
        try {
            $token = $request->get('token');
            $newpassword = $request->get('newpassword');
            $cpassword = $request->get('cpassword');
            // dd($token,$newpassword,$cpassword);
            if ($newpassword === $cpassword) {

                $this->_customer->updatePasswordSubmit($request->all());
                return redirect('http://localhost:4000/login');
                // return response()->json(['data' => $res,  'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }
    // public function paymentProcess(Request $request){
    //     dd("adfasdf");
    // }


    public function paymentProcess(Request $request){

        $this->validate($request, [
            'stripe_payment_id' => 'required|unique:payments',
            'property_id' => 'required|unique:payments',
            'user_id' => 'required',
            'amount' => 'required',
        ]);

        $data = $request->all();

        try {
            
                $res = $this->_payment->makeStripePayment($data);
                
                return response()->json(['data' => $res,  'message' => HttpStatusCode::$statusTexts[HttpStatusCode::OK]], HttpStatusCode::OK);
            
        } catch (\Exception $e) {
            return response()->json(['message' => HttpStatusCode::$statusTexts[HttpStatusCode::INTERNAL_SERVER_ERROR]], HttpStatusCode::INTERNAL_SERVER_ERROR);
        }

        
    }
}
