<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('readings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('zone_id')->nullable();
            $table->integer('officer_id')->nullable();
            $table->integer('account_id')->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('meter_number', 100)->nullable();
            $table->date('period')->nullable();
            $table->string('curr_reading', 50)->nullable();
            $table->string('prev_reading', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->dateTime('date_read')->nullable();
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
        Schema::dropIfExists('readings');
    }
}
