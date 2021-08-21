<?php

namespace App\Models;

use App\Events\CreateCustomerForgotPasswordEvent;
use Illuminate\Support\Str;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Utils\AppConst;
use App\Http\Resources\ProfileResource;
use App\Notifications\Reserve;
use App\Utils\UserType;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Employee;
use App\Events\CreateForgotPasswordEvent;
use App\Events\CreateMemberForgotPasswordEvent;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;

class User extends Model
{
    use Authenticatable, Authorizable, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 'type', 'first_name', 'last_name', 'email', 'password',
        'username', 'gender', 'city', 'ph', 'state', 'country', 'cnic', 'marital_status',
        'address', 'zip_code', 'verification_code', 'is_verified' , 'token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Relation With Employee
     *
     * @return void
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    /**
     * relationship with property
     *
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    /**
     * Relationship With Customers
     *
     * @return void
     */
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Relationship With Companies
     *
     * @return void
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
/**
     * Relationship With Payment
     *
     * @return void
     */

   

    /**
     * boot function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->first_name = Str::ucfirst($model->first_name);
            $model->last_name = Str::ucfirst($model->last_name);
            $model->city = Str::ucfirst($model->city);
            $model->state = Str::ucfirst($model->state);
            $model->country = Str::ucfirst($model->country);
            $model->password = hash(AppConst::HASH_ALGO, $model->password);
        });


        self::created(function ($model) {
        });

        self::updating(function ($model) {

            // $model->first_name = Str::ucfirst($model->first_name);
            // $model->last_name = Str::ucfirst($model->last_name);
            // $model->city = Str::ucfirst($model->city);
            // $model->state = Str::ucfirst($model->state);
            // $model->country = Str::ucfirst($model->country);
            // $model->password = hash(AppConst::HASH_ALGO, $model->password);
        });
    }


    /**
     *
     * Update profile data
     */
    public function updateProfile($data, $id)
    {
        /**
         * find user and update data
         */

        if (Auth::user()->type == UserType::CUSTOMER) {
            $customer_id = Auth::user()->customer->id;
            
            $customerData['bank_name'] = $data['bank_name'];
            $customerData['account_title'] = $data['account_title'];
            $customerData['account_no'] = $data['account_no'];
            $customerData['iban'] = $data['iban'];
            // $customerData = $data->except([
            //     'user_id', 'name', 'photo', 'is_verified' , 'is_overseas'
            // ]);

            Customer::where('id', $customer_id)->update($customerData);
        }

        $userData = $data->except([
            'company_id', 'type', 'marital_status', 'street_address', 'verification_code', 'is_verified', 'password','iban','account_no','bank_name','account_title'
        ]);
        User::where('id', $id)->update($userData);
    }



   

     /**
     * store image process
     *
     * @return srting
     */
    public function storeImage($image)
    {
        $imageName = uniqid().'-'.time().".".$image->getClientOriginalExtension();
        $destination = 'uploads/company/images/';
        $path= $image->move($destination,$imageName);
        return $path;
    }

     /**
     *
     * reset member password mail
     */
    public function resetMemberPasswordMail($email)
    {

        $user = User::where('email', $email)->first();
        $reSetUrl = route('restoreMemberPassword', [
            'token' => $user['token']
        ]);
        // dd($reSetUrl);
        $data = $user;
        $data['reSetUrl'] = $reSetUrl;

        event(new CreateMemberForgotPasswordEvent($data));
        return new UserResource($data);
    }


     /**
     *
     * update member password
     */
    public function updateMemberPasswordSubmit($data)
    {
        $customerData['token'] = $data['token'];
        $customerData['password'] = hash(AppConst::HASH_ALGO,  $data['newpassword']);

        User::where('token', $data['token'])->update($customerData);
    }

    /**
     * Save logo image for company
     * @param string $formData
     * @param int $id
     * 
     * @return image photo
     */
    // public function updateLogo($formData, $id)
    // {
    //     $company= Company::find($id);
    //     if ($formData->hasFile('logo')){
    //         $oldlogo = $company->logo;
    //         if(file_exists($oldlogo)){
    //             unlink($oldlogo);
    //         }
    //         $imagePath = $this->storeImage($formData->logo);
    //         $company['logo'] = $imagePath;
    //     }
    //     $company->save();
    // }

    
}
