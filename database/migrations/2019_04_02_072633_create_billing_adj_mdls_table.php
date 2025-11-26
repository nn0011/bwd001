<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingAdjMdlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_adj_mdls', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('acct_id')->nullable();
			$table->string('acct_no', 30)->nullable();
			$table->date('date1')->nullable();
			$table->datetime('date1_stamp')->nullable();
			$table->string('ref_no', 30)->nullable();
			$table->double('amount', 8, 2)->nullable();
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
        Schema::dropIfExists('billing_adj_mdls');
    }
}
