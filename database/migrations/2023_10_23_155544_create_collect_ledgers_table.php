<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collect_ledgers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coll_id')->nullable();//
            $table->integer('acct_id')->nullable();//
            $table->integer('admin_id')->nullable();//
            $table->integer('led_id')->nullable();//
            $table->double('amount',2)->nullable();//
            $table->double('tax_val',2)->nullable();//
            $table->double('discount_val',2)->nullable();//
            $table->text('collect_raw')->nullable();//
            $table->text('collect_clean')->nullable();//
            $table->string('pay_type', 20)->nullable();//
            $table->date('coll_date')->nullable();//
            $table->text('pycy')->nullable();//

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
        Schema::dropIfExists('collect_ledgers');
    }
}
