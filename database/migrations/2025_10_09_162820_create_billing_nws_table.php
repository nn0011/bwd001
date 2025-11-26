<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingNwsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_nws', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acct_id')->nullable();//
            $table->integer('paya_id')->nullable();//

            $table->integer('id1')->nullable();//
            $table->integer('id2')->nullable();//
            $table->integer('id3')->nullable();//
            
            $table->string('status', 20)->nullable();
            $table->string('typ', 20)->nullable();
            $table->string('title', 100)->nullable();
            $table->string('remark', 200)->nullable();
            $table->string('code1', 30)->nullable();

            $table->double('amt_1',8,2)->nullable();//
            $table->double('amt_2',8,2)->nullable();//
            $table->double('amt_3',8,2)->nullable();//
            $table->double('amt_4',8,2)->nullable();//

            $table->date('date1')->nullable();//
            $table->date('date2')->nullable();//
            $table->date('date3')->nullable();//



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
        Schema::dropIfExists('billing_nws');
    }
}
