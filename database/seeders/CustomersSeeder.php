<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Utils\MyAppEnv;
use App\Models\Customer;

class CustomersSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        if(is_app_env([MyAppEnv::LOCAL,MyAppEnv::DEVELOPMENT,MyAppEnv::STAGING,])){
            Customer::truncate();
        }

        // Other Customer
        Customer::insert([
            [
            'user_id' => 5,
            'name' => 'Bilal',
            'bank_name' => 'HBL',
            'account_no' => '224567',
            'account_title' => '566745',
            'iban'=>'225432',
            'is_overseas'=>true,
            'is_verified'=>true,
            'created_at' =>\Carbon\Carbon::now('utc'),
            'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
            'user_id' => 6,
            'name' => 'Zuahib',
            'bank_name' => 'NationalBank',
            'account_no' => '211767',
            'account_title' => '522245',
            'iban'=>'2255443',
            'is_overseas'=>true,
            'is_verified'=>true,
            'created_at' =>\Carbon\Carbon::now('utc'),
            'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
            'user_id' => 7,
            'name' => 'Tayyab',
            'bank_name' => 'HBL',
            'account_no' => '200767',
            'account_title' => '55645',
            'iban'=>'443377',
            'is_overseas'=>true,
            'is_verified'=>true,
            'created_at' =>\Carbon\Carbon::now('utc'),
            'updated_at' =>\Carbon\Carbon::now('utc'),
            ]
        ]);
    }
}
