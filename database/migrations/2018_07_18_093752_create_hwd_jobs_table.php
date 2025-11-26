<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHwdJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hwd_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('jtype', 100)->nullable();
            $table->string('jstatus', 100)->nullable();
            $table->text('jcmd')->nullable();            
            $table->text('jdata')->nullable();            
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
        Schema::dropIfExists('hwd_jobs');
    }
}
