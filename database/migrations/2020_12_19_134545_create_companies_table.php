<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('logo', 150)->nullable();
            $table->string('email', 40)->nullable();
            $table->string('ph', 15)->nullable();
            $table->string('fax', 15)->nullable();
            $table->string('location', 60)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('city', 60)->nullable();
            $table->string('state', 60)->nullable();
            $table->string('country', 70)->nullable();
            $table->string('zip_code', 12)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->smallInteger('total_employees')->default(false);
            $table->text('about')->nullable();
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
        Schema::dropIfExists('companies');
    }
}
