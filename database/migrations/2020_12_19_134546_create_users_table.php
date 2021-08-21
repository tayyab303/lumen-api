<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->tinyInteger('type');
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('username', 60)->unique();
            $table->string('email', 60)->unique();
            $table->string('password', 64);
            $table->string('gender',2)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('city', 60)->nullable();
            $table->string('state', 60)->nullable();
            $table->string('country', 60)->nullable();
            $table->string('zip_code', 12)->nullable();
            $table->string('ph', 15)->nullable();
            $table->string('cnic', 13)->nullable();
            $table->string('marital_status',2)->nullable();
            $table->string('street_address', 60)->nullable();
            $table->integer('verification_code')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('token',64);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
