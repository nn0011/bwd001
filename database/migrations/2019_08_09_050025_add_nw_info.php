<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNwInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
			$table->string('nw_reff', 50)->nullable();
			$table->string('nw_type', 30)->nullable();
			$table->text('nw_desc')->nullable();			
        });

        Schema::table('ledger_datas', function (Blueprint $table) {
			$table->string('nw_reff', 50)->nullable();
			$table->string('nw_type', 30)->nullable();
			$table->text('nw_desc')->nullable();			
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
        
        Schema::table('ledger_datas', function (Blueprint $table) {
            //
        });
        
    }
}
