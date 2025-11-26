<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLedgerDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ledger_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acct_id')->nullable();
            $table->integer('bill_id')->nullable();
            $table->integer('read_id')->nullable();
			$table->double('arrear', 8, 2)->nullable()->default(0);
			$table->double('billing', 8, 2)->nullable()->default(0);
			$table->double('payment', 8, 2)->nullable()->default(0);
			$table->double('discount', 8, 2)->nullable()->default(0);
			$table->double('penalty', 8, 2)->nullable()->default(0);
			$table->double('ttl_bal', 8, 2)->nullable()->default(0);
            $table->text('ledger_info')->nullable()->default('');
            $table->date('date01')->nullable()->useCurrent();
            $table->date('period')->nullable();
            $table->string('status', 20)->nullable()->default('');
            $table->string('led_type', 20)->nullable()->default('');
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
        Schema::dropIfExists('ledger_datas');
    }
}
