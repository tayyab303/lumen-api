<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installments_log', function (Blueprint $table) {
            $table->id();
            $table->integer('property_id');
            $table->integer('price');
            $table->string('loan_period', 30);
            $table->integer('down_payment');
            $table->integer('loan_amount');
            $table->integer('monthly_installment');
            $table->string('name', 60);
            $table->string('ph',15);
            $table->string('city',30);
            $table->text('details',200);
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
        Schema::dropIfExists('installments_log');
    }
}
