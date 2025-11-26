<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCtype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hwd_ledgers', function (Blueprint $table) {
             $table->string('ctyp1', 20)->nullable()->default('');
             $table->string('c_typ2', 20)->nullable()->default('');
             $table->string('c_typ3', 20)->nullable()->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hwd_ledgers', function (Blueprint $table) {
            //
        });
    }
}
