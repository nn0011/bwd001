<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgingRecievablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aging_recievables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->string('account_num')->nullable();
            $table->string('full_name')->nullable();
            $table->integer('acct_type_id')->nullable();
            $table->integer('zone_id')->nullable();
            $table->date('period')->nullable();
            $table->text('data1')->nullable();
            $table->text('data2')->nullable();
            $table->double('current_balance', 8, 2)->nullable();
            $table->string('rtype')->nullable();
            $table->timestamps();

            //$table->index(['full_name', 'zone_id', 'period', 'account_num', 'acct_type_id']);
            $table->index(['full_name']);
            $table->index(['zone_id']);
            $table->index(['period']);
            $table->index(['account_num']);
            $table->index(['acct_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aging_recievables');
    }
}
