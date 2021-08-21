<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Utils\UserType;
use App\Utils\AppConst;
use App\Utils\PropertyStatus;
use App\Events\PropertyStatusEvent;
use App\Notifications\Status;
use App\Notifications\Rejected;
use App\Notifications\Reserve;
use App\Notifications\CommentNotify;

use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;

class Comment extends Model
{
    /**
     * use of trait 
     * 
     */
    use Notifiable;
    
    protected $fillable=[
        'property_id', 'company_id', 'employee_id',  'comment', 'status','admin', 'longitude', 'latitude',
    ];

    /**
     * relation with property
     * 
     * @return void
     */
    public function property(){
        return $this->belongsTo(Property::class);
    }
    
    /**
     * relation with user
     * 
     * @return void
     */
    public function user(){
        return $this->belongsTo(User::class);
    }
    
    /**
     * relation with employee
     * 
     * @return void
     */
    public function employee(){
        return $this->belongsTo(Employee::class);
    }

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
     * Create comment on on the property
     *
     * @param Request $comment
     * @return $result
     */
    public function createComment($createComment, $id)
    {
        $property = Property::with('employee.user', 'company.user')->find($id);
        $company =  Company::find($property->company_id);
        $employee =  Employee::find($property->employee_id);
        $createComment['property_id'] = $property->id;
        if(auth()->user()->type === UserType :: SUPER_ADMIN){
            $property['status'] = $createComment->status;
            if($property['status'] == PropertyStatus::APPROVED){
                $property['is_verified'] =  AppConst::VERIFIED;
            }else if($property['status'] == PropertyStatus::REJECTED){
                $property['is_verified'] =  AppConst::UNVERIFIED;
            }
            $property->save();
            if ($company !== null ) {
                $company->notify(new Status($property));
            }
            else {
                $employee->notify(new Status($property));
            }
            if ($company !== null) {
            $company = $property->company->email;
            event(new PropertyStatusEvent($property , $company));
            }
        }
        $result = $this->calcCoordinates($property->latitude, $property->longitude, $createComment);
        return $result;
    }

    public function calcCoordinates($latitude, $longitude, $params)
    {
        static $x = M_PI / 180;
        $latitude *= $x;
        $longitude *= $x;
        $params->latitude *= $x;
        $params->longitude *= $x;
        $distance = 2 * asin(sqrt(pow(sin(($latitude - $params->latitude) / 2), 2) + cos($latitude) * cos($params->latitude) * pow(sin(($longitude - $longitude) / 2), 2)));
        $distance = $distance * 6378137;
        if((auth()->user()->type === UserType :: SUPER_EMPLOYEE || auth()->user()->type === UserType :: COMPANY_EMPLOYEE) && $distance <= 500){
            $params['employee_id'] = auth()->user()->employee->id;
        } else if(auth()->user()->type === UserType :: SUPER_ADMIN){
            $params['admin'] = auth()->user()->username;
        } else if(auth()->user()->type === UserType::COMPANY){
            $params['company_id'] = auth()->user()->company_id;
        } else {
            return false;
        }
        $user = User::find(1);
        $result = Comment::create($params->all());
        if(auth()->user()->type === UserType :: SUPER_EMPLOYEE || auth()->user()->type === UserType :: COMPANY_EMPLOYEE || auth()->user()->type === UserType :: COMPANY){
            $user->notify(new CommentNotify($result->property_id));
        }
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

        self::creating(function($model){
            $model->comment = Str::ucfirst($model->comment);

        });

        self::created(function($model){
        });
    }
}
