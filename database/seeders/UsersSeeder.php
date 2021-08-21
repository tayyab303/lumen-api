<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Utils\AppConst;
use App\Utils\UserType;
use App\Utils\Gender;
use App\Utils\MyAppEnv;
use App\Models\User;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(is_app_env([MyAppEnv::LOCAL,MyAppEnv::DEVELOPMENT,MyAppEnv::STAGING,])){
            User::truncate();
        }

        $this->addSuperAdmin();

        // Other Users
        User::insert([
            [
                // 2 company admin
                'type' => UserType::COMPANY,
                'company_id' => 1,
                'first_name' => 'Faizan',
                'last_name' => 'Raheem',
                'username' => 'cadmin',
                'email' => 'admin@test.com',
                'password' => hash(AppConst::HASH_ALGO, 'admin123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236655487',
                'zip_code' => '58745',
                'city' => 'Lahore',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 3 company manager
                'type' => UserType::COMPANY,
                'company_id' => 2,
                'first_name' => 'Aslam',
                'last_name' => 'Latif',
                'username' => 'manager',
                'email' => 'manager@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236655487',
                'zip_code' => '45202',
                'city' => 'Islamaabad',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 4 Servay Inspector
                'type' => UserType::COMPANY,
                'company_id' => 3,
                'first_name' => 'Atif',
                'last_name' => 'Bajwa',
                'username' => 'inspector',
                'email' => 'inspector@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236655487',
                'zip_code' => '45202',
                'city' => 'Karachi',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 5
                'type' => UserType::CUSTOMER,
                'company_id' => null,
                'first_name' => 'Kashif',
                'last_name' => 'Khan',
                'username' => 'customer',
                'email' => 'customer@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236655487',
                'zip_code' => '45202',
                'city' => 'Sialkot',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 6
                'type' => UserType::CUSTOMER,
                'company_id' => null,
                'first_name' => 'Kashif',
                'last_name' => 'Khan',
                'username' => 'customer1',
                'email' => 'customer1@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236655487',
                'zip_code' => '45202',
                'city' => 'Gujranwala',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 7
                'type' => UserType::CUSTOMER,
                'company_id' => null,
                'first_name' => 'Kashif',
                'last_name' => 'Khan',
                'username' => 'customer2',
                'email' => 'customer2@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236655487',
                'zip_code' => '45202',
                'city' => 'Lahore',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 8
                'type' => UserType::SUPER_EMPLOYEE,
                'company_id' => null,
                'first_name' => 'Ali',
                'last_name' => 'Khan',
                'username' => 'employee',
                'email' => 'employee@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236560322',
                'zip_code' => '45202',
                'city' => 'Lahore',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 9
                'type' => UserType::COMPANY_EMPLOYEE,
                'company_id' => null,
                'first_name' => 'Salman',
                'last_name' => 'Khan',
                'username' => 'employee1',
                'email' => 'employee1@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236560323',
                'zip_code' => '45542',
                'city' => 'Karachi',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 10
                'type' => UserType::SUPER_EMPLOYEE,
                'company_id' => null,
                'first_name' => 'Saad',
                'last_name' => 'Khan',
                'username' => 'employee2',
                'email' => 'employee2@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236560323',
                'zip_code' => '45542',
                'city' => 'Karachi',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
            [
                // 11
                'type' => UserType::COMPANY_EMPLOYEE,
                'company_id' => null,
                'first_name' => 'Aman',
                'last_name' => 'Zaman',
                'username' => 'employee4',
                'email' => 'employee4@test.com',
                'password' => hash(AppConst::HASH_ALGO, '123123'),
                'gender' => Gender::MALE,
                'ph'=>'+924236560443',
                'zip_code' => '45542',
                'city' => 'Karachi',
                'is_verified'=>true,
                'token'=>Str::random(64),
                'created_at' =>\Carbon\Carbon::now('utc'),
                'updated_at' =>\Carbon\Carbon::now('utc'),
            ],
        ]);
    }

    public function addSuperAdmin(){
        /***
         * User-Admin
         */
        User::create([
            'type' => UserType::SUPER_ADMIN,
            'first_name' => 'Rashid',
            'last_name' => 'Hussain',
            'username' => 'sadmin',
            'email' => 'sadmin@test.com',
            'password' => 'admin123',
            'gender' => Gender::MALE,
            'ph'=>'+924236655487',
            'zip_code' => '56456',
            'city' => 'Lahore',
            'is_verified'=>true,
            'token'=>Str::random(64),
        ]);
    }

}


