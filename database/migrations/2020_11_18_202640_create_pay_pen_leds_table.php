<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayPenLedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_pen_leds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->nullable();
            $table->integer('cid')->nullable();
            $table->integer('pen_id')->nullable();
            $table->double('amt',8,2)->nullable();
            $table->string('typ')->default('penalty')->nullable();
            $table->string('status')->default('active')->nullable();
            $table->integer('adj_id')->nullable();
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
        Schema::dropIfExists('pay_pen_leds');
    }
}
