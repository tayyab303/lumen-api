<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('name', 60)->nullable();
            $table->string('photo')->nullable();
            $table->string('bank_name', 60)->nullable();
            $table->string('account_title', 60)->nullable();
            $table->string('account_no', 20)->nullable();
            $table->string('iban', 35)->nullable();
            $table->boolean('is_overseas')->nullable()->default(false);
            $table->boolean('is_verified')->nullable()->default(false);
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
        Schema::dropIfExists('customers');
    }
}
