<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Payment;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Events\CreateCustomerEvent;
use App\Events\CreateCustomerForgotPasswordEvent;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\CustomerResource;

class Customer extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'photo', 'bank_name', 'account_title', 'account_no', 'iban',
        'is_overseas', 'is_verified'
    ];

    /**
     * Reletionship with User
     * 
     * @return User Object
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Create new Customer
     *
     * @param mixed $formData
     * @return var array
     */
    public function createCustomer($formData)
    {
        /**
         * store user data
         */
        $user = $formData->all();
        $user['type'] = UserType::CUSTOMER;
        $user['verification_code'] = mt_rand(100000, 999999);
        $user['token'] = Str::random(64);
        $user = User::create($user);
        $customer = $formData->all();
        $customer['user_id'] = $user->id;
        $customer = Customer::create($customer);
        $verifyUrl = route('customerVerify', [
            'id' => $user->id, 'code' => $user['verification_code']
        ]);
        $customerData = Customer::with('user')->has('user')->find($customer->id);
        $customerData['verifyUrl'] = $verifyUrl;
        event(new CreateCustomerEvent($customerData));
        return new CustomerResource($customer);
    }

    /**
     * Update Customer Details
     *
     * @param mixed $formData
     * @param int $id
     * @return var array
     */
    public function updateCustomer($updateForm, $id)
    {

        $customer = Customer::find($id);
        $data = $updateForm->except('photo');
        $customer->update($data);
        $userData = $updateForm->except([
            'name', 'photo', 'bank_name', 'account_title', 'account_no', 'iban', 'is_overseas'
        ]);
        User::where('id', $customer->user_id)->update($userData);
        return new CustomerResource(Customer::with('user')->has('user')->find($customer->id));
    }

    /**
     * store image to public folder
     *
     * @return image path
     */
    public function storeImage($image)
    {
        $imageName = uniqid() . '-' . time() . "." . $image->getClientOriginalExtension();
        $destination = 'uploads/customer/images/';
        $path = $image->move($destination, $imageName);
        return $path;
    }

    /**
     * Save photo for customer function
     * @param string $formData
     * @param int $id
     * 
     * @return image photo
     */
    public function customerImage($formData, $id)
    {
        $customer = Customer::find($id);
        if ($formData->hasFile('photo')) {
            $oldImage = $customer->photo;
            if (file_exists($oldImage)) {
                unlink($oldImage);
            }
            $imagePath = $this->storeImage($formData->photo);
            $customer['photo'] = $imagePath;
        }
        $customer->save();
    }

    /**
     * Verify Customer function
     *
     * @param User int $id
     * @param User verification int $code
     * @return void
     */
    public function verifyCustomer($VerificationData)
    {
        $user = User::find($VerificationData->id);
        if (!$user) {
            return "Incorrent URL!";
        }
        if ($user->verification_code ===  intval($VerificationData->code) && $user->is_verified != true) {
            $arrData = $user->toArray();
            // $arrData['is_verified'] = AppConst::YES;
            User::find($VerificationData->id)->update(['is_verified' => AppConst::YES]);
            return "You are successfully Verified";
        } else {
            return "You are already verified";
        }
    }

    /**
     * Function For Search,Sorting and Filtering Customers
     * @param array $params
     * @return array
     */
    public function searchCustomers($params)
    {
        $result = User::with('customer')->Where('type', '=', UserType::CUSTOMER)->when($params['search'], function ($query, $search) {
            $searchValues = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            return $query->where(function ($qry) use ($searchValues) {
                foreach ($searchValues as $value) {
                    $qry->orwhere('first_name', 'LIKE', "%$value%")->orwhere('last_name', 'LIKE', "%$value%")->orWhere('city', 'LIKE', "%$value%")->orWhere('state', 'LIKE', "%$value%")->orWhere('country', 'LIKE', "%$value%")->orWhere('ph', 'LIKE', "%$value%")->orWhere('email', 'LIKE', "%$value%")->orWhere('zip_code', 'LIKE', "%$value%");
                }
            });
        })->when($params['bank'], function ($query, $bank) {
            $query = Customer::with('user')->where('bank_name', '=', $bank);
            return $query;
        })->when($params['sorting'], function ($query, $sort) {
            if ($sort['sort'] == AppConst::ASC) {
                return $query->orderby($sort['column'], AppConst::ASC);
            } else if ($sort['sort'] == AppConst::DESC) {
                return $query->orderby($sort['column'], AppConst::DESC);
            }
        })->paginate(AppConst::PAGE_SIZE);

        return $result;
    }



    /**
     *
     * reset password mail
     */
    public function resetPasswordMail($email)
    {

        $user = User::where('email', $email)->first();
        $reSetUrl = route('restorePassword', [
            'token' => $user['token']
        ]);

        $data = Customer::with('user')->has('user')->find($user->customer->id);
        $data['reSetUrl'] = $reSetUrl;

        event(new CreateCustomerForgotPasswordEvent($data));
        return new CustomerResource($data);
    }


    /**
     *
     * update password
     */
    public function updatePasswordSubmit($data)
    {
        $customerData['token'] = $data['token'];
        $customerData['password'] = hash(AppConst::HASH_ALGO,  $data['newpassword']);

        User::where('token', $data['token'])->update($customerData);
    }



    /**
     * boot function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->name = Str::title($model->name);
            $model->bank_name = Str::ucfirst($model->bank_name);
        });

        self::created(function ($model) {
        });
        self::updating(function ($model) {
            $model->name = Str::title($model->name);
            $model->bank_name = Str::ucfirst($model->bank_name);
        });
    }
}
