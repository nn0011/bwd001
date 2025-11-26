<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrintServsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_servs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('zone_id')->nullable();
            $table->integer('bill_id')->nullable();
            $table->string('bill_start', 50)->nullable()->default('');
            $table->string('acct_start', 50)->nullable()->default('');
            $table->date('period')->nullable();
            $table->string('status', 20)->nullable()->default('active');
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
        Schema::dropIfExists('print_servs');
    }
}
