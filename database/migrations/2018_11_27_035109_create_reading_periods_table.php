<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReadingPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reading_periods', function (Blueprint $table) {
            $table->increments('id');
             $table->string('status', 20)->nullable()->default('');
            $table->date('period')->nullable();
            $table->integer('ttl_acct')->nullable()->default(0);
            $table->integer('ttl_read')->nullable()->default(0);
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
        Schema::dropIfExists('reading_periods');
    }
}
