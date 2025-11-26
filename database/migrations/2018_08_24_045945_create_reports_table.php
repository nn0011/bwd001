<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
             $table->increments('id');
             $table->integer('user_id')->nullable();
             $table->string('account_num')->nullable();
             $table->string('full_name')->nullable();
             $table->integer('acct_type_id')->nullable();
             $table->integer('acct_status_id')->nullable();
             $table->integer('zone_id')->nullable();
             $table->date('period')->nullable();
             $table->double('billing_total', 8, 2)->nullable();
             $table->double('collected', 8, 2)->nullable();
             $table->string('rtype')->nullable();
             $table->string('status')->nullable();
             $table->text('data1')->nullable();
             $table->text('data2')->nullable();
             $table->text('ageing_data')->nullable();
             $table->timestamps();

             $table->index(['full_name']);
             $table->index(['zone_id']);
             $table->index(['period']);
             $table->index(['account_num']);
             $table->index(['acct_type_id']);
             $table->index(['rtype']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
