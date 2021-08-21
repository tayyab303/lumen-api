<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    /**
     * use of trait 
     * 
     */
    use SoftDeletes;
    
    /**
     * protected date
     * 
     * @return void
    */
    protected $date =['deleted_at'];

    /**
     * the array that is assignable 
    **/
    protected $fillable=['property_id', 'image'];

    /**
     * relation with property
     * 
     * @return void
     */
    public function property(){
        return $this->belongsTo(Property::class);
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

        });

        self::created(function($model){

        });
    }
}


