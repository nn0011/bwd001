<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConnectionLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connection_ledgers', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('acct_id')->nullable();
			$table->integer('bill_id')->nullable();
			$table->integer('penalty_id')->nullable();
			$table->string('acct_no', 30)->nullable();
			$table->string('status', 30)->nullable();
			$table->string('typ1', 30)->nullable();
			$table->text('remaks')->nullable();
			$table->text('datas')->nullable();
			$table->date('date1')->nullable();
			
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
        Schema::dropIfExists('connection_ledgers');
    }
}
