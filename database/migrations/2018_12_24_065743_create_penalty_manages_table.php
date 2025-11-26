<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenaltyManagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penalty_manages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->nullable()->default('');
            $table->text('message')->nullable()->default('');
            $table->string('status', 20)->nullable()->default('active');
            $table->date('date01')->nullable()->useCurrent();
            $table->date('period')->nullable()->useCurrent();
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
        Schema::dropIfExists('penalty_manages');
    }
}
