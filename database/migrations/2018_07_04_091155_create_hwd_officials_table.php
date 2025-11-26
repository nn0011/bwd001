<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHwdOfficialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hwd_officials', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->nullable();
            $table->string('fname', 100)->nullable();
            $table->string('lname', 100)->nullable();
            $table->string('mi', 20)->nullable();
            $table->string('address1', 150)->nullable();
            $table->string('zones', 100)->nullable();
            $table->string('stat', 50)->nullable();
            $table->string('typ1', 50)->nullable();
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
        Schema::dropIfExists('hwd_officials');
    }
}
