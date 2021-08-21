<?php

namespace Database\Seeders;

use App\Utils\EmployeeType;
use App\Utils\Gender;
use App\Utils\MyAppEnv;
use App\Models\User;
use App\Models\Employee;

use Illuminate\Database\Seeder;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (is_app_env([MyAppEnv::LOCAL, MyAppEnv::DEVELOPMENT, MyAppEnv::STAGING,])) {
            Employee::truncate();
        }


        Employee::insert([
            [
                'user_id' => 8,
                'company_id'=>null,
                'type' => EmployeeType::ADMIN,
                'joining_salary' => 30000,
                'working_hours' => '08:00:00',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ], 
            [
                'user_id' => 9,
                'company_id'=>1,
                'type' => EmployeeType::MANAGER,
                'joining_salary' => 55000,
                'working_hours' => '09:00:00',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ],
            [
                'user_id' => 10,
                'company_id'=>null,
                'type' => EmployeeType::SURVEY_INSPECTOR,
                'joining_salary' => 20000,
                'working_hours' => '10:00:00',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ],
            [
                'user_id' => 11,
                'company_id'=>1,
                'type' => EmployeeType::SURVEY_INSPECTOR,
                'joining_salary' => 20000,
                'working_hours' => '10:00:00',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ]
        ]);
    }
}
