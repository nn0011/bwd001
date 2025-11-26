<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingMdlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_mdls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reading_id')->nullable();
            $table->integer('rate_id')->nullable();
            $table->string('adj_reading', 20)->nullable();
            $table->string('period', 20)->nullable();
            $table->string('status', 20)->nullable();
            $table->date('bill_date')->nullable();
            $table->text('remarks', 20)->nullable();
            $table->integer('prep_by')->nullable();
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
        Schema::dropIfExists('billing_mdls');
    }
}
