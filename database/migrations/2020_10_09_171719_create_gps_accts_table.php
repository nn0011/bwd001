<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGpsAcctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_accts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acct_id')->nullable();
            $table->string('mtr_n',50)->nullable();
            $table->string('lat1',50)->nullable();
            $table->string('lng1',50)->nullable();
            $table->string('stat',30)->nullable();
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
        Schema::dropIfExists('gps_accts');
    }
}
