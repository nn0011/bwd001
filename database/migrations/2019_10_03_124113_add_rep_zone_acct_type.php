<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRepZoneAcctType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report1s', function (Blueprint $table) {
			$table->integer('zone_id')->nullable();
			$table->integer('acct_type')->nullable();
			$table->decimal('penalty', 8,2)->default(0)->nullable();
			
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report1s', function (Blueprint $table) {
            //
        });
    }
}
