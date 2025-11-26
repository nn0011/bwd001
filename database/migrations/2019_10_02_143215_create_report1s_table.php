<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReport1sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report1s', function (Blueprint $table) {
            $table->increments('id');
            
			$table->string('rep_type',50)->nullable();
			
			$table->string('reff_1',100)->nullable();
			$table->string('reff_2',100)->nullable();
			$table->string('reff_3',100)->nullable();
			$table->string('reff_4',100)->nullable();
			
			$table->integer('acct_id')->nullable();
			$table->integer('coll_id')->nullable();
			$table->integer('bill_id')->nullable();
			$table->integer('acct_stat')->nullable();
			
			$table->decimal('fcollected', 8,2)->default(0)->nullable();
			$table->decimal('fcurrent', 8,2)->default(0)->nullable();
			$table->decimal('farrear', 8,2)->default(0)->nullable();
			$table->decimal('fprv_arr', 8,2)->default(0)->nullable();
			$table->decimal('fnon_wat', 8,2)->default(0)->nullable();
			$table->decimal('ftax', 8,2)->default(0)->nullable();
			$table->decimal('fdis', 8,2)->default(0)->nullable();
			$table->decimal('fadjust', 8,2)->default(0)->nullable();
			
			$table->date('dperiod')->nullable();
			$table->datetime('dpaydate')->nullable();
			
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
        Schema::dropIfExists('report1s');
    }
}
