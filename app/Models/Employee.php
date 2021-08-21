<?php

namespace App\Models;

use App\Models\User;
use App\Utils\UserType;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\EmployeeResource;
use Illuminate\Support\Str;
use App\Utils\AppConst;
use App\Events\CreateEmployeeEvent;
use App\Notifications\Status;
use App\Notifications\Assign;

use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;


class Employee extends Model
{
    /**
     * use of trait 
     * 
     */
    use  Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'type', 'photo', 'joining_salary', 'current_salary', 'working_hours', 
        'joining_date', 'quit_date','company_id'
    ];

    /**
     * Relation with User
     *
     * @return void
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with company
     *
     * @return void
     */
    public function company(){
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation with property
     *
     * @return void
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
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
     * Store Employee
     *
     * @return var array
     */
    public function createEmployee($formData){
        /**
         * store user data
         */
        $user = $formData->all();
        if(auth()->user()->company_id){
            $user['type'] = UserType::COMPANY_EMPLOYEE;
        }
        else if(auth()->user()->type === UserType::SUPER_ADMIN ){
            $user['type'] = UserType::SUPER_EMPLOYEE;
        }
        $user['password'] = Str::random(8);
        $password= $user['password'] ;
        $result = User::create($user);
        $employee = $formData->all();
        $employee['user_id'] = $result->id;
        $employee['company_id'] = auth()->user()->company_id;
        $employee = Employee::create($employee);
        $employeeData= Employee::with('user')->has('user')->find($employee->id);
        event(new CreateEmployeeEvent($employeeData, $password));
        return new EmployeeResource($employeeData);
    }

    /**
    *
    * Update Employee
    *
    * @return var array
    */
    public function updateEmployee($updateForm,$id){
        /**
         * find employee and update data
         */
        $employee = Employee::find($id);
        $data = $updateForm->except('photo');
        $employee->update($data);
        $userData = $updateForm->except([
            'type', 'photo', 'working_hours', 'joining_salary', 'current_salary', 'joining_date', 'quit_date', 'password'
        ]);
        User::where('id',$employee->user_id)->update($userData);
        return new EmployeeResource(Employee::with('user')->has('user')->find($employee->id));
    }

    /**
     * store image to public folder
     *
     * @return image path
     */
    public function storeImage($image)
    {
        $imageName = uniqid().'-'.time().".".$image->getClientOriginalExtension();
        $destination = 'uploads/employee/images/';
        $path= $image->move($destination,$imageName);
        return $path;
    }

    /**
     * Save photo for employee function
     * @param string $formData
     * @param int $id
     * 
     * @return image photo
     */
    public function employeeImage($formData, $id)
    {
        $employee= Employee::find($id);
        if ($formData->hasFile('photo')){
            $oldImage = $employee->photo;
            if(file_exists($oldImage)){
                unlink($oldImage);
            }
            $imagePath = $this->storeImage($formData->photo);
            $employee['photo'] = $imagePath;
        }
        $employee->save();
    }
    
    /**
     * Function For Search,Sorting and Filtering Employees
     * @param array $params
     * @return array
     */
    public function searchEmployees($params)
    {
        $result = Employee::when(auth()->user()->type === UserType::COMPANY, function ($query, $type) {
            return $query->where('company_id', '=', auth()->user()->company->id);
        })->with('user')->whereHas('user', function ($query) use($params) {
            $query->when($params['city'], function ($query, $city) {
                return $query->where('city', '=', $city );
            })->when($params['search'], function ($query, $search) {
                $searchValues = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
                return $query->where(function ($qry) use ($searchValues) {
                    foreach ($searchValues as $value) {
                        $qry->orwhere('first_name', 'LIKE', "%$value%")->
                        orwhere('last_name', 'LIKE', "%$value%")->
                        orWhere('city', 'LIKE', "%$value%")->
                        orWhere('state', 'LIKE', "%$value%")->
                        orWhere('country', 'LIKE', "%$value%")->
                        orWhere('ph', 'LIKE', "%$value%")->
                        orWhere('email', 'LIKE', "%$value%")->
                        orWhere('zip_code', 'LIKE', "%$value%");
                    }
                });
            })
            ->when($params['salary'], function ($query, $salary){
                return $query->where('current_salary', '=', $salary );
            });
            // when($params['sort'], function ($query, $sort) {
            //     if($sort==AppConst::ASC){
            //         return $query->orderby('city', AppConst::ASC);
            //     }else if($sort==AppConst::DESC){
            //         // dd($sort['column']);
            //         return $query->orderby('city', AppConst::DESC);
            //     }
            // });
        })->paginate(AppConst::PAGE_SIZE);
            return $result;
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
        });

        self::created(function ($model) {
        });
    }
}
