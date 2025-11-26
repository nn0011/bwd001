<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingDuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_dues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_id')->nullable();
            $table->double('due_amount', 8, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->string('due_stat', 20)->nullable();
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
        Schema::dropIfExists('billing_dues');
    }
}
