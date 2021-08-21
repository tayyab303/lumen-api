<?php

namespace App\Models;

use App\Utils\AppConst;
use App\Utils\UserType;
use Illuminate\Support\Str;
use App\Events\CreateCompanyEvent;
use App\Http\Resources\CompaniesResource;
use App\Http\Resources\CompanyResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Status;
use App\Notifications\Rejected;
use App\Notifications\Reserve;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    /**
     * use of trait 
     * 
     */
    use  Notifiable;
    // use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'logo', 'email', 'ph', 'fax',
        'location', 'address', 'city', 'state',
        'country', 'zip_code', 'is_verified', 'about'
    ];

    /**
     * relationship with property
     *  */
    public function properties(){
        return $this->hasMany(Property::class);
    }
    /**
     * relationship with employee
     *  */
    public function employees(){
        return $this->hasMany(Employee::class);
    }
    /**
     * Relationship With User
     *
     * @return void
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**   
     * relationship with comments
     * 
     * @return void 
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    /**
    *
    * Create Company
    *
    * @return void
    */
    public function createCompany($formData){
        /**
         * check incoming logo
         */
        $company = $formData->all();
        $companyData['name']=$company['name'];
        $companyData['email']=$company['company_email'];
        $companyData['ph']=$company['company_ph'];
        $companyData['fax']=$company['fax'];
        $companyData['address']=$company['company_address'];
        // $companyData['location']=$company['location'];
        $companyData['city']=$company['company_city'];
        $companyData['state']=$company['company_state'];
        $companyData['country']=$company['company_country'];
        $companyData['zip_code']=$company['company_zip_code'];
        $companyData['about']=$company['about'];
        $result = Company::create($companyData);
        /**
         * store user data
         */
        $user = $formData->except('company_email');
        $user['password'] = Str::random(8);
        $password= $user['password'] ;
        $user['company_id'] = $result->id;
        $user['type'] = UserType::COMPANY;
        User::create($user);
        $company= Company::with('user')->has('user')->find($result->id);
        event(new CreateCompanyEvent($company , $password));
        return new CompanyResource($company);
    }

    /**
    *
    * Update Company
    *
    * @return void
    */
    public function updateCompany($updateForm,$id){
        /**
         * find company and update data
         */
        $company = Company::find($id);
        $companyData['name']=$updateForm['name'];
        $companyData['email']=$updateForm['company_email'];
        $companyData['ph']=$updateForm['company_ph'];
        $companyData['fax']=$updateForm['fax'];
        $companyData['address']=$updateForm['company_address'];
        $companyData['city']=$updateForm['company_city'];
        $companyData['state']=$updateForm['company_state'];
        $companyData['country']=$updateForm['company_country'];
        $companyData['zip_code']=$updateForm['company_zip_code'];
        $companyData['about']=$updateForm['about'];
        $company->update($companyData);
        $userData = $updateForm->except([
            'name','company_email','company_ph','location',
            'company_address','company_city', 'company_state', 'about',
            'company_country','company_zip_code','logo', 'fax'
        ]);
        
        // $userData['password'] = hash(AppConst::HASH_ALGO, $userData['password']);
        User::where('company_id',$company->id)->update($userData);
        return new CompanyResource(Company::with('user')->has('user')->find($id));
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
     * Save logo image for company
     * @param string $formData
     * @param int $id
     * 
     * @return image photo
     */
    public function companyLogo($formData, $id)
    {
        $company= Company::find($id);
        if ($formData->hasFile('logo')){
            $oldlogo = $company->logo;
            if(file_exists($oldlogo)){
                unlink($oldlogo);
            }
            $imagePath = $this->storeImage($formData->logo);
            $company['logo'] = $imagePath;
        }
        $company->save();
    }

    /**
     * Function For Search,Sorting and Filtering Companies
     * @param array $params
     * @return array
     */
    public function searchCompanies($params)
    {
        $result=Company::with('user')->has('user')->when($params['status'], function ($query, $status) {
            if($status == 'no'){
                return $query->where('is_verified', '=',AppConst::UNVERIFIED );
            }else{
                return $query->where('is_verified', '=',AppConst::VERIFIED );
            }
        })->when($params['city'], function ($query, $city) {
            return $query->where('city', '=', $city );
        })->when($params['state'], function ($query, $state) {
            return $query->where('state', '=', $state );
        })->when($params['search'], function ($query, $search) {
            $searchValues = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            return $query->where(function ($qry) use ($searchValues) {
                foreach ($searchValues as $value) {
                    $qry->orwhere('name', 'LIKE', "%$value%")->
                    orWhere('city', 'LIKE', "%$value%")->
                    orWhere('state', 'LIKE', "%$value%")->
                    orWhere('ph', 'LIKE', "%$value%")->
                    orWhere('email', 'LIKE', "%$value%")->
                    orWhere('zip_code', 'LIKE', "%$value%");
                }
            });
        })->when($params['sorting'], function ($query, $sort) {
            if($sort['sort'] == AppConst::ASC){
                return $query->orderby($sort['column'], AppConst::ASC);
            }else if($sort['sort'] == AppConst::DESC){
                return $query->orderby($sort['column'], AppConst::DESC);
            }
        })->latest()->paginate(AppConst::PAGE_SIZE);

        return $result;
        // if(count($result)>0 && $params != null){
        //     return new CompaniesResource($result);
        // }else{
        //     if($params == null){
        //         $result = Company::with('user')->has('user')->latest()->paginate(AppConst::PAGE_SIZE);
        //         return new CompaniesResource($result);
        //     }else {
        //         return $result = ['search' => 'Search not found'];
        //     }
        // }
    }

    /**
     * boot function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->name = Str::title($model->name);
            $model->city = Str::ucfirst($model->city);
            $model->state = Str::ucfirst($model->state);
            $model->country = Str::ucfirst($model->country);
        });

        self::created(function($model){

        });

        self::updating(function($model)
        {
            $model->name = Str::title($model->name);
            $model->city = Str::ucfirst($model->city);
            $model->state = Str::ucfirst($model->state);
            $model->country = Str::ucfirst($model->country);
        });
    }
}
