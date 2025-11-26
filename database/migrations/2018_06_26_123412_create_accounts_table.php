<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('acct_no', 100)->nullable();
            $table->string('fname', 100)->nullable();
            $table->string('lname', 100)->nullable();
            $table->string('mi', 20)->nullable();
            $table->string('address1', 150)->nullable();
            $table->string('address2', 150)->nullable();
            $table->string('tel1', 30)->nullable();
            $table->string('fax1', 30)->nullable();
            $table->date('residence_date')->nullable();
            $table->integer('num_of_bill')->nullable();
            $table->integer('zone_id')->nullable();
            $table->integer('acct_type_key')->nullable();
            $table->integer('acct_status_key')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->dateTime('acct_created_date')->nullable();
            $table->dateTime('acct_modified_date')->nullable();
            $table->string('status', 30)->nullable();
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
        Schema::dropIfExists('accounts');
    }
}
