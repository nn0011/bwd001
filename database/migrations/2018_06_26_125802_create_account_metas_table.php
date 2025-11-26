<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('meta_name', 100)->nullable();
            $table->string('meta_code', 100)->nullable();
            $table->string('meta_desc', 200)->nullable();
            $table->string('meta_type', 30)->nullable();
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
        Schema::dropIfExists('account_metas');
    }
}
