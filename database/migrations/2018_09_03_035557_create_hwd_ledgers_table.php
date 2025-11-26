<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHwdLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hwd_ledgers', function (Blueprint $table) {
            $table->increments('id');

            $table->string('led_type', 50)->nullable();
            $table->string('led_title', 100)->nullable();

            $table->string('status1', 20)->nullable();
            $table->string('status2', 20)->nullable();

            $table->text('led_desc1')->nullable();
            $table->text('led_desc2')->nullable();
            $table->text('led_desc3')->nullable();

            $table->text('led_data1')->nullable();
            $table->text('led_data2')->nullable();
            $table->text('led_data3')->nullable();

            $table->integer('led_key1')->nullable();
            $table->integer('led_key2')->nullable();
            $table->integer('led_key3')->nullable();

            $table->date('led_date1')->nullable();
            $table->dateTime('led_date2')->nullable();
            $table->dateTime('led_date3')->nullable();

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
        Schema::dropIfExists('hwd_ledgers');
    }
}
