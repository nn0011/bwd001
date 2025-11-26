<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPenaltyDatePenStat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_bill_zones', function (Blueprint $table) {
            $table->string('pen_stat', 30)->nullable();
            $table->date('pen_date')->nullable();
            $table->date('period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_bill_zones', function (Blueprint $table) {
            //
        });
    }
}
