<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Category extends Model
{
    protected $fillable=['name'];

    /**
     * relation with property
     * 
     * @return void
     */
    public function properties(){
        return $this->belongsToMany(Property::class);
    }

    /**   
     * store categories 
     * 
     * @return string category 
     */
    public function addCategory($createCategory)
    {
        $category= Category::create($createCategory->all());
        return $category;
    }

    /**   
     * update category
     * 
     * @return string category 
     */
    public function updateCategory($updateCategory)
    {
        $category= Category::find($updateCategory->id);
        $category->update($updateCategory->all());
        return $category;
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

        });

        self::created(function($model){

        });

        // self::updating(function($model)
        // {
        //     $model->name = Str::title($model->name);

        // });
    }
}


