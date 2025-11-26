<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherPayablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_payables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('paya_title', 100)->nullable();
            $table->string('paya_desc', 300)->nullable();
            $table->string('paya_stat', 20)->nullable();
			$table->double('paya_amount', 8, 2)->nullable();
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
        Schema::dropIfExists('other_payables');
    }
}
