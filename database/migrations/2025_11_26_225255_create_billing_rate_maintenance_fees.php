<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingRateMaintenanceFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_rate_maintenance_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nratehdid')->nullable();//
            $table->integer('account_type_id')->nullable();//
            $table->integer('meter_size_id')->nullable();//
			$table->decimal('min_charge', 8,2)->default(0)->nullable();
			$table->decimal('fee', 8,2)->default(0)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
