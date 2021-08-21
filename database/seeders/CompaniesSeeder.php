<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Utils\MyAppEnv;
use App\Models\Company;

class CompaniesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(is_app_env([MyAppEnv::LOCAL,MyAppEnv::DEVELOPMENT,MyAppEnv::STAGING,])){
            Company::truncate();
        }

        Company::insert([
            [
                'name' => 'Ferhan Real Estate',
                'city' => 'LHR',
                'state' => 'Punjab',
                'zip_code' => '45487',
                'about'=>"Lorem Ipsum is simply dummy text of the printing and typesetting industry. and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
                'email' => 'test1@test.com',
                'ph'=>'+924236655487',
                'is_verified'=>true,
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],[
                'name' => 'Chouhan Real Estate',
                'city' => 'KHR',
                'state' => 'Sindh',
                'zip_code' => '54654',
                'about' => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or'",
                'email' => 'test2@test.com',
                'ph'=>'+924236655488',
                'is_verified'=>true,
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],[
                'name' => 'Kamran Real Estate',
                'city' => 'FSB',
                'state' => 'Blochistan',
                'zip_code' => '546834',
                'about' => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or'",
                'email' => 'company3@test.com',
                'ph'=>'+924236655489',
                'is_verified'=>true,
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ]
        ]);
    }
}
