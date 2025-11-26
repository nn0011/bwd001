<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHwdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hwd_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reff_id')->nullable();
            $table->string('req_type', 50)->nullable();
            $table->string('remarks', 200)->nullable();
            $table->string('status', 50)->nullable();
            $table->text('other_datas')->nullable();
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
        Schema::dropIfExists('hwd_requests');
    }
}
