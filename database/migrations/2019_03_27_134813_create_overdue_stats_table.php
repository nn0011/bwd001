<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOverdueStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overdue_stats', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('zone_id')->nullable()->default(0);
			$table->date('date1')->nullable();
            $table->date('period')->nullable();
			$table->integer('status')->nullable()->default(0);
			$table->string('remarks', 200)->nullable();
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
        Schema::dropIfExists('overdue_stats');
    }
}
