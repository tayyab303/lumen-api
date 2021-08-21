<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Notifications\Reserve;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;

class Reservation extends Model
{
    /**
     * use of trait 
     * 
     */
    use  Notifiable;

    public $table = "reservation";
    protected $fillable=['property_id', 'name', 'phone_number', 'email', 'message'];

    /**
     * relation with property
     * 
     * @return void
     */
    public function property(){
        return $this->belongsTo(Property::class);
    }

    /**   
     * store reservation  details 
     * 
     * @return string formData 
     */
    public function createReservation($FormData, $id)
    {
        $property = Property::with('company')->find($id);
        $user = User::find(1);
        $company = $property->company;
        $FormData['property_id'] = $property->id;
        $FormData= Reservation::create($FormData->all());
        if($company === null ){
            $user->notify(new Reserve($property));
        }else {
            $company->notify(new Reserve($property));
        }
        return $FormData;
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

            $model->name = Str::ucfirst($model->name);

        });

        self::created(function($model){
        });
    }
}


