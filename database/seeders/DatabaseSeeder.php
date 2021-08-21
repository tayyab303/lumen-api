<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        //disable foreign key check for this connection before running seeders
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('');
        $this->call(CompaniesSeeder::class);
        $this->command->info('Companies Table Seeded Successfully!');

        $this->command->info('');
        $this->call(UsersSeeder::class);
        $this->command->info('Users Table Seeded Successfully!');

        $this->command->info('');
      
        $this->call(PropertiesSeeder::class);
        $this->command->info('Properties Table Seeded Successfully!');

        $this->call(CategoriesSeeder::class);
        $this->command->info('Categories Table Seeded Successfully!');


        $this->call(CustomersSeeder::class);
        $this->command->info('Customer Table Seeded Successfully!');
      
        $this->call(EmployeesSeeder::class);
        $this->command->info('Employees Table Seeded Successfully!');
      
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }
}
