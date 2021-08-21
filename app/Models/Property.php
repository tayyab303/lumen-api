<?php

namespace App\Models;


use Illuminate\Support\Str;
use App\Utils\AppConst;
use App\Utils\UserType;
use App\Models\Employee;
use App\Utils\PropertyStatus;
use App\Events\AssignPropertyEvent;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\PropertyResource;
use App\Notifications\Assign;

use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;



use Illuminate\Database\Eloquent\SoftDeletes;

class Property extends Model
{
    /**
     * use of trait 
     * 
     */
    use SoftDeletes, Notifiable;
    
    /**
     * protected date
     * 
     * @return void
    */
    protected $date =['deleted_at'];
    
    /**
     * the array that is assignable 
    **/
    protected $fillable=[
        'company_id', 'employee_id', 'title', 'price_sqft', 'price', 'unit_area',
        'is_constructed', 'is_balted','total_floors','kitchen', 'bathrooms', 'bedrooms',
        'is_corner', 'is_flat', 'is_for_rent','is_installment_available', 'building_year',
        'covered_area', 'total_rooms', 'longitude', 'latitude','description','
        gerage','status', 'is_available', 'is_verified', 'society', 'phase','block', 
        'address', 'zip_code','city', 'state', 'country','unit_type'
        
    ];

    /**   
     * relationship with company
     * 
     * @return void 
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**   
     * relationship with user
     * 
     * @return void 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Relationship With Payment
     *
     * @return void
     */
    public function Payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**   
     * relationship with images
     * 
     * @return void 
     */
    public function images()
    {
        return $this->hasMany(Media::class);
    }

    /**   
     * relationship with reservations
     * 
     * @return void 
     */
    public function Reservation()
    {
        return $this->hasMany(Reservation::class);
    }

    /**   
     * relationship with categories
     * 
     * @return void 
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
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
     * relationship with employee
     * 
     * @return void 
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);                                                                                                                                                                                                                                                                (Employee::class);
    }
    
    /**
     * store images of property to public folder
     * @param string $image
     * @return string
     */
    public function propertyImage($image)
    {
        $imageName = uniqid().'-'.time().".".$image->getClientOriginalExtension();
        $destinationPath = 'uploads/property/images/';
        $imagePath= $image->move($destinationPath,$imageName);
        return $imagePath;
    }

    /**   
     * store property , with  multiple images 
     * 
     * @param $formData
     * @return json response 
     */
    public function createProperty($formData)
    {
        $formData['company_id']=auth()->user()->company_id;
        $property= Property::create($formData->except('image'));
        $categories= json_decode($formData->category_id);
        $property->categories()->attach($categories);
     
        if($formData->hasFile('image')) {
            foreach($formData->file('image') as $image) {
                $imagePath= $this->propertyImage($image);
                Media::create([
                    'property_id' => $property->id,
                    'image' => $imagePath,
                ]);
            }
        }
        return new PropertyResource(Property::with('images','categories')->find($property->id));
    }
    
    /**   
     * update property 
     * 
     * @param $updateForm
     * @return json response 
     */
    public function updateProperty($updateForm)
    {
        $property = Property::find($updateForm->id);
        $property->update($updateForm->except('image'));
        $categories= json_decode($updateForm->category_id);
        $property->categories()->sync($categories);

        if($updateForm->hasFile('image')) {
            $images = Media::where('property_id','=',$updateForm->id)->get();
            foreach($images as $file) {
                $oldImage = $file->image;
                if(file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            $property->images()->delete();
            foreach($updateForm->file('image') as $image) {
                $imagePath= $this->propertyImage($image);
                Media::create([
                    'property_id' => $property->id,
                    'image' => $imagePath,
                ]);
            }
        }
        return new PropertyResource(Property::with('images','categories')->find($property->id));
    }

    /**   
     * Searching  functionality with sorting,filteing proepry 
     * 
     * @param array $params
     * @return array
     */
    public function searchProperty($params)
    {
        $search = Property::where(function ($query) use ($params) {
            return $this->commonSearch($query, $params);
        })->paginate(AppConst::PAGE_SIZE);
        return $search;
    }

    /**   
     * Searching  functionality with listing of property  
     * 
     * @param array $params
     * @return array
     */
    public function listProperty($params)
    {
        $result = Property::when(auth()->user()->type === UserType::COMPANY, function ($query, $type) {
            return $query->where('company_id', '=', auth()->user()->company->id);
        })
        ->when(auth()->user()->type === UserType::SUPER_EMPLOYEE || auth()->user()->type === UserType::COMPANY_EMPLOYEE, function ($query, $type) {
            return $query->where('employee_id', '=', auth()->user()->employee->id);
        })->when($params['available'], function ($query, $available){

            if($available == PropertyStatus::AVAILABLE)
            {
                return  $query->where('is_available', '=',$available);
            }
            else if($available == PropertyStatus::SOLD)
            {
                return  $query->where('is_available', '=',$available);
            }
        })->when($params['status'], function ($query, $status){

            if($status == PropertyStatus::APPROVED)
            {
                return  $query->where('status', '=',$status);
            }
            else if($status == PropertyStatus::REJECTED)
            {
                return  $query->where('status', '=',$status);
            }
            else if($status == PropertyStatus::PENDING)
            {
                return  $query->where('status', '=',$status);
            }
        })
        ->when($params['verify'], function ($query, $verify){

            if($verify == AppConst::VERIFIED)
            {
                return  $query->where('is_verified', '=',$verify);
            }
            else if($verify == AppConst::UNVERIFIED)
            {
                return  $query->where('is_verified', '=',$verify);
            }
        })
        ->when($params['sort'], function ($query, $sort){
            if($sort['order'] == AppConst::ASC){
                return $query->orderby($sort['column'], AppConst::ASC);
            }
            else if($sort['order'] == AppConst::DESC){
                return $query->orderby($sort['column'], AppConst::DESC);
            }
        })
        ->where(function ($query) use ($params) {
            return $this->commonSearch($query, $params);
        })->with('employee.user')->latest()->paginate(AppConst::PAGE_SIZE);;
        return $result;
    }

    public function commonSearch($query, $params)
    {
        $result = $query->when($params['property_type'], function ($query, $property_type) {
            if ($property_type == PropertyStatus::BUY) {
                return  $query->where('is_for_rent', '=', AppConst::NO);
            } else {
                return  $query->where('is_for_rent', '=', AppConst::YES);
            }
        })
        ->when($params['unit_type'], function ($query, $unit_type){
            return $query->where('unit_type', '=', $unit_type );
        })
        ->when($params['min_price'], function ($query, $min_price){
            return $query->where('price', '>=', $min_price );
        })
        ->when($params['max_price'], function ($query, $max_price){
            return $query->where('price', '<=', $max_price );
        })
        ->when($params['city'],function($query, $city){
            return $query->where('city', '=', $city)->get();
        })
        ->when($params['area'],function($query, $area){
            return $query->where('unit_area', '=', $area);
        })
        ->when($params['beds'],function($query, $beds){
            return $query->where('bedrooms', '=', $beds);
        })
        ->when($params['country'],function($query, $phase){
            return $query->where('country', '=', $phase);
        })
        ->when($params['search'], function ($query, $search){
            $searchkeys = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            return $query->where(function ($q) use ($searchkeys){
                foreach ($searchkeys as $key){
                    $q->orwhere('title', 'LIKE', "%$key%")
                    ->orWhere('price', 'LIKE', "%$key%")
                    ->orWhere('unit_area', 'LIKE', "%$key%")
                    ->orWhere('bedrooms', 'LIKE', "%$key%")
                    ->orWhere('total_rooms', 'LIKE', "%$key%")
                    ->orWhere('city', 'LIKE', "%$key%")
                    ->orWhere('state', 'LIKE', "%$key%")
                    ->orWhere('address', 'LIKE', "%$key%")
                    ->orWhere('society', 'LIKE', "%$key%")
                    ->orWhere('block', 'LIKE', "%$key%")
                    ->orWhere('phase', 'LIKE', "%$key%")
                    ->orWhere('country', 'LIKE', "%$key%")
                    ->orWhere('total_floors', 'LIKE', "%$key%")
                    ->orWhere('description', 'LIKE', "%$key%")
                    ->orWhere('zip_code', 'LIKE', "%$key%")
                    ->orWhere('is_for_rent', 'LIKE', "%$key%");
                }
            });
        });
        return $result;
    }

    /**   
     * employee list for task  
     * 
     * @param int $id
     * @return var array 
     */
    public function viewEmployee($id)
    {
        $property = Property::find($id);
        $city = $property->city;
        $query = Employee::with('user')->whereHas('user', function ($query) use($city) {
            if(auth()->user()->type === UserType::SUPER_ADMIN){
                $query->where('city', '=', $city )->where('type', '=', UserType::SUPER_EMPLOYEE );
            }
            else if(auth()->user()->company_id && auth()->user()->type === UserType::COMPANY){
                $query->where('city', '=', $city )->where('type', '=', UserType::COMPANY_EMPLOYEE )->where('company_id', '=', auth()->user()->company_id );

            }
            else if($query->where('city', '!=', $city ) && auth()->user()->type === UserType::SUPER_ADMIN) {
                return Employee::with('user')->where('type', '=', UserType::SUPER_EMPLOYEE);
            }
            
            else if($query->where('city', '!=', $city ) && auth()->user()->type === UserType::COMPANY) {
                return Employee::with('user')->where('company_id', '=', auth()->user()->company_id );
            }
        })->paginate(AppConst::PAGE_SIZE);
        if (count($query) > 0) {
            return $query;
        } else {
        return Employee::with('user')->where('company_id', '=', auth()->user()->company_id )->paginate(AppConst::PAGE_SIZE);
    }
}

    /**   
     * Assign property to employee
     * 
     * @param $params
     * @return var array 
     */
    public function assignProperty($params)
    {
        $property=Property::find($params->id);
        if($property->employee_id == null){
            $property->employee_id = $params->employee_id;
            $property->save();
            $employee = Employee::with('user')->find($property->employee_id);
            $company = Company::with('user')->find($property->employee_id);
            $employee->notify(new Assign($property));
            $company->notify(new Assign($property));
            $employeeName= $employee->user->first_name.' '.$employee->user->last_name;
            event(new AssignPropertyEvent($employee, $property));
            // dd(auth()->user()->notifications);
            return ["employee" => $employee, "message" => "Property Successfully Assigned to:".$employeeName];
        } else{
            $employee = Employee::with('user')->find($property->employee_id);
            $employeeName= $employee->user->first_name.' '.$employee->user->last_name;
            return ["employee" => $employee,"message" => "property already assigned to:".$employeeName];
        }
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
            
            $model->title = Str::title($model->title);
            $model->address = Str::ucfirst($model->address);
            $model->society = Str::title($model->society);
            $model->city = Str::ucfirst($model->city);
            $model->state = Str::ucfirst($model->state);
            $model->country = Str::ucfirst($model->country);

        });

        self::created(function($model){

        });

        self::updating(function($model)
        {
            $model->title = Str::title($model->title);
            $model->address = Str::ucfirst($model->address);
            $model->society = Str::title($model->society);
            $model->city = Str::ucfirst($model->city);
            $model->state = Str::ucfirst($model->state);
            $model->country = Str::ucfirst($model->country);

        });
    }
}
