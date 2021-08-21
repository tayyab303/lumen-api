<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Notifications\Reserve;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;

class Installment extends Model
{
    /**
     * use of trait 
     * 
     */
    use  Notifiable;

    public $table = "installments_log";
    protected $fillable=['property_id', 'price', 'down_payment', 'loan_period', 'details', 'ph', 'name', 'city', 'loan_amount', 'monthly_installment', ];

    /**
     * relation with property
     * 
     * @return void
     */
    public function property(){
        return $this->belongsTo(Property::class);
    }

    /**   
     * store loan  details 
     * 
     * @return string formData 
     */
    public function createLog($FormData, $id)
    {
        $property = Property::with('company')->find($id);
        $FormData['property_id'] = $property->id;
        $FormData= Installment::create($FormData->all());
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
            $model->city = Str::ucfirst($model->city);

        });

        self::created(function($model){
        });
    }
}


