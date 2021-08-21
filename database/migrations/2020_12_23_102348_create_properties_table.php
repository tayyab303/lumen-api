<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     *Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->string('title', 60);
            $table->decimal('price', 16, 2);
            $table->decimal('price_sqft', 12, 2)->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('unit_type');
            $table->integer('unit_area');
            $table->boolean('is_corner')->default(false);
            $table->boolean('is_for_rent')->default(false);
            $table->boolean('is_flat')->default(false);
            $table->boolean('is_constructed')->default(false);
            $table->boolean('is_balted')->default(false); // this column is related to convey about ploting 
            $table->boolean('is_installment_available')->nullable()->default(false);
            $table->integer('kitchen')->nullable()->default(0);
            $table->integer('bathrooms')->nullable()->default(0);
            $table->integer('bedrooms')->nullable()->default(0);
            $table->integer('gerage')->nullable()->default(0);
            $table->integer('covered_area')->nullable();
            $table->integer('total_rooms')->nullable()->default(0);
            $table->integer('total_floors')->nullable()->default(0);
            $table->integer('status')->nullable()->default(2);
            $table->boolean('is_available')->nullable()->default(true);
            $table->boolean('is_verified')->nullable()->default(false);
            $table->string('society',60)->nullable();
            $table->string('phase',4)->nullable();
            $table->string('block',30)->nullable();
            $table->string('address',124);
            $table->string('zip_code',12)->nullable();
            $table->string('city',60);
            $table->string('state',60);
            $table->string('country',60);
            $table->text('description',500);
            $table->string('building_year',20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
