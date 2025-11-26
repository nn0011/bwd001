<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdjustmentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_adj_mdls', function (Blueprint $table) {
			$table->string('adj_typ', 50)->nullable();
			$table->text('adj_typ_desc')->nullable();
			$table->date('adj_period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_adj_mdls', function (Blueprint $table) {
            //
        });
    }
}
