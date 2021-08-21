<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Utils\MyAppEnv;
use App\Models\Property;


class PropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     *@return void
     */
    public function run()
    {
        if (is_app_env([MyAppEnv::LOCAL, MyAppEnv::DEVELOPMENT, MyAppEnv::STAGING,])) {
            Property::truncate();
        }
        Property::insert([
            [
                'company_id' => 1,
                'employee_id' => null,
                'title' => 'Avenue',
                'price' => 700000,
                'price_sqft' => 120000,
                'longitude' => 00.3,
                'latitude' => 002.3,
                'unit_area' => 1254,
                'unit_type' =>2,
                'is_corner' => 0,
                'is_for_rent' => 1,
                'is_flat' => 0,
                'kitchen' => 2,
                'bathrooms' => 2,
                'bedrooms' => 2,
                'total_floors' => 2,
                'status' => true,
                'address' => 'lahore12',
                'city' => 'islambad',
                'country' => 'pakistan',
                'state' => 'punjab',
                'zip_code' => 123,
                'description' => '123dsss',
                'gerage'=> '1',
                'society' => 'walton',
                'phase' => '5',
                'block' => 'A',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ],
            [
                'company_id' => 2,
                'employee_id' => null,
                'title' => 'Villa',
                'price' => 900000,
                'price_sqft' => 120000,
                'longitude' => 00.3,
                'latitude' => 002.3,
                'unit_area' => 1254,
                'unit_type' =>2,
                'is_corner' => 0,
                'is_for_rent' => 1,
                'is_flat' => 0,
                'kitchen' => 2,
                'bathrooms' => 2,
                'bedrooms' => 2,
                'total_floors' => 2,
                'status' => true,
                'address' => 'lahore12',
                'city' => 'lahore',
                'country' => 'pakistan',
                'state' => 'punjab',
                'zip_code' => 123,
                'description' => '123dsss',
                'gerage'=> '1',
                'society' => 'walton',
                'phase' => '6',
                'block' => 'D',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ],
            [
                'company_id' => 1,
                'employee_id' => null,
                'title' => 'luxirious flats',
                'price' => 700000,
                'price_sqft' => 120000,
                'longitude' => 00.3,
                'latitude' => 002.3,
                'unit_area' => 1254,
                'unit_type' =>2,
                'is_corner' => 0,
                'is_for_rent' => 1,
                'is_flat' => 0,
                'kitchen' => 2,
                'bathrooms' => 2,
                'bedrooms' => 2,
                'total_floors' => 2,
                'status' => true,
                'address' => 'lahore12',
                'city' => 'faislabad',
                'country' => 'pakistan',
                'state' => 'pubjab',
                'zip_code' => 123,
                'description' => '123dsss',
                'gerage'=> '1',
                'society' => 'walton',
                'phase' => '6',
                'block' => 'E',
                'created_at' => \Carbon\Carbon::now('utc'),
                'updated_at' => \Carbon\Carbon::now('utc'),
            ]
        ]);
    }
}
