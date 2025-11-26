<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CbillArrPenDisco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
			$table->decimal('bill1', 8,2)->default(0)->nullable();
			$table->decimal('arrear1', 8,2)->default(0)->nullable();
			$table->decimal('penalty1', 8,2)->default(0)->nullable();
			$table->decimal('discount1', 8,2)->default(0)->nullable();
			$table->decimal('adjust1', 8,2)->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            //
        });
    }
}
