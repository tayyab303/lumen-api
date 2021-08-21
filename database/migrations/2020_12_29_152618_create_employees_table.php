<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->tinyInteger('type');
            $table->string('photo')->nullable();
            $table->decimal('joining_salary', 8, 2)->nullable();
            $table->decimal('current_salary', 8, 2)->nullable();
            $table->time('working_hours')->nullable();
            $table->date('joining_date')->nullable();
            $table->date('quit_date')->nullable();
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
        Schema::dropIfExists('employees');
    }
}
