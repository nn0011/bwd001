<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_checks', function (Blueprint $table) {
             $table->increments('id');
             $table->integer('coll_id')->nullable();
             $table->integer('bank_id')->nullable();
             $table->double('amount', 8,2)->nullable();
             $table->string('remarks', 100)->nullable();
             $table->string('check_num', 100)->nullable();
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
        Schema::dropIfExists('payment_checks');
    }
}
