<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collector_id')->nullable();
            $table->string('collection_type')->nullable();
            $table->string('collection_cat')->nullable();
            $table->integer('billing_id')->nullable();
            $table->string('invoice_num', 100)->nullable();
			$table->double('payment', 8, 2)->nullable();
			$table->double('balance_payment', 8, 2)->nullable();
            $table->string('status')->nullable();
			$table->dateTime('payment_date')->nullable();
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
        Schema::dropIfExists('collections');
    }
}
