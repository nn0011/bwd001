<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeterHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meter_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acct_id')->nullable();//
            $table->integer('meter_id')->nullable();//
            $table->integer('admin_id')->nullable();//
            $table->string('served_date')->nullable();//
            $table->string('served_name', 100)->nullable();//
            $table->string('remaks', 100)->nullable();//
            $table->string('typ', 30)->nullable();//
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
        Schema::dropIfExists('meter_histories');
    }
}
