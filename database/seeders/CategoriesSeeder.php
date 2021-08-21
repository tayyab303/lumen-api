<?php

namespace Database\Seeders;

use App\Utils\MyAppEnv;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        if (is_app_env([MyAppEnv::LOCAL, MyAppEnv::DEVELOPMENT, MyAppEnv::STAGING,])) {
            Category::truncate();
        }
        Category::insert([
            [
                'name' =>'Commercial',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ],
            [
                'name' =>'Residential',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ],
            [
                'name'=>'Land ',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ],
            [
                'name'=>'Luxirous Flats',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ]
        ]);
    }
}
