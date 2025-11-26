<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_reports', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('acct_id')->nullable();//
            $table->integer('user_id')->nullable();//
            
            $table->integer('key1')->nullable();//
            $table->integer('key2')->nullable();//

            $table->dateTime('date1')->nullable();//
            $table->dateTime('date2')->nullable();//

            $table->text('data1')->nullable();//
            $table->text('data2')->nullable();//

            $table->double('amt_1',8,2)->nullable();//
            $table->double('amt_2',8,2)->nullable();//

            $table->string('status', 20)->nullable();
            $table->string('typ1', 20)->nullable();
            $table->string('str1', 150)->nullable();

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
        Schema::dropIfExists('temp_reports');
    }
}
