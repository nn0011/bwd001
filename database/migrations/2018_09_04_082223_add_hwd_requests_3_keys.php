<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHwdRequests3Keys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hwd_requests', function (Blueprint $table) {
             $table->string('skey1', 30)->nullable();
             $table->integer('ikey1')->nullable();
             $table->date('dkey1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hwd_requests', function (Blueprint $table) {
            //
        });
    }
}
