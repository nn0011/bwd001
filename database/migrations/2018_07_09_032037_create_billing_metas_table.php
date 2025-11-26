<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('meta_name', 100)->nullable();
            $table->string('meta_code', 30)->nullable();
            $table->text('meta_desc')->nullable();
            $table->text('meta_data')->nullable();
            $table->string('meta_value', 50)->nullable();
            $table->string('meta_type', 30)->nullable();
            $table->date('meta_date', 30)->nullable();
            $table->string('status', 30)->nullable();
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
        Schema::dropIfExists('billing_metas');
    }
}
