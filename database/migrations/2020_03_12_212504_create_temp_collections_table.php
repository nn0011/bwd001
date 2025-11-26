<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_collections', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('collector_id')->nullable();
			$table->integer('cust_id')->nullable();
			$table->integer('zone_id')->nullable();
			$table->integer('bank_id')->nullable();
			$table->integer('billing_id')->nullable();
			
			$table->string('collection_type',50)->nullable();
			$table->string('collection_cat',50)->nullable();
			$table->string('invoice_num',50)->nullable();
			$table->string('status',50)->nullable();
			$table->string('pay_type',50)->nullable();
			$table->string('check_no',50)->nullable();
			$table->string('bank_info',100)->nullable();
			$table->string('nw_reff',50)->nullable();
			$table->string('nw_type',50)->nullable();
			$table->string('nw_desc',100)->nullable();
			$table->string('nw_glsl',50)->nullable();


			$table->decimal('payment', 8,2)->default(0)->nullable();
			$table->decimal('balance_payment', 8,2)->default(0)->nullable();
			$table->decimal('amt_rec', 8,2)->default(0)->nullable();
			$table->decimal('c1', 8,2)->default(0)->nullable();
			$table->decimal('a1', 8,2)->default(0)->nullable();
			$table->decimal('a2', 8,2)->default(0)->nullable();
			$table->decimal('tax_per', 8,2)->default(0)->nullable();
			$table->decimal('tax_val', 8,2)->default(0)->nullable();
			$table->decimal('bill1', 8,2)->default(0)->nullable();
			$table->decimal('arrear1', 8,2)->default(0)->nullable();
			$table->decimal('penalty1', 8,2)->default(0)->nullable();
			$table->decimal('discount1', 8,2)->default(0)->nullable();
			$table->decimal('adjust1', 8,2)->default(0)->nullable();
			$table->decimal('chk_full', 8,2)->default(0)->nullable();

			$table->dateTime('payment_date')->nullable();
			
			$table->integer('upload_id')->nullable();
			$table->string('up_fname',100)->nullable();
			
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
        Schema::dropIfExists('temp_collections');
    }
}
