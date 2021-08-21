<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Mail\ContactUsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'ph', 'description'
    ];

    public function contactUs($formData)
    {
        $result = Contact::create($formData);
        Mail::to(env('ADMIN_MAIL'))->cc($result['email'])->send(new ContactUsMail(($result)));
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
            $model->name = Str::ucfirst($model->name);
         
        });
        
        
        self::created(function($model){

        });

        self::updating(function($model)
        {
        
            $model->name = Str::ucfirst($model->name);
          
        });
    }
}