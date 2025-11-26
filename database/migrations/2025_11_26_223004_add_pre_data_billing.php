<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreDataBilling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_mdls', function (Blueprint $table) {

			$table->decimal('bill_franchise_tax', 8,2)->default(0)->nullable();
			$table->decimal('bill_maintenance_fee', 8,2)->default(0)->nullable();

            $table->integer('acct_stat')->nullable();//
            $table->integer('acct_zone')->nullable();//
            $table->integer('acct_sr')->nullable();//
            $table->integer('acct_size')->nullable();//
            $table->string('billed_stat', 30)->nullable();//
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_mdls', function (Blueprint $table) {
            //
        });
    }
}
