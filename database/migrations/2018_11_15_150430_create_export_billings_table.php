<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_billings', function (Blueprint $table) {
            $table->increments('id');
             $table->string('A', 50)->nullable()->default('');
             $table->string('B', 50)->nullable()->default('');
             $table->string('C', 50)->nullable()->default('');
             $table->string('D', 50)->nullable()->default('');
             $table->string('E', 50)->nullable()->default('');
             $table->string('F', 50)->nullable()->default('');
             $table->string('G', 50)->nullable()->default('');
             $table->string('H', 50)->nullable()->default('');
             $table->string('I', 50)->nullable()->default('');
             $table->string('J', 50)->nullable()->default('');
             $table->string('K', 50)->nullable()->default('');
             $table->string('L', 50)->nullable()->default('');
             $table->string('M', 50)->nullable()->default('');
             $table->string('N', 50)->nullable()->default('');
             $table->string('O', 50)->nullable()->default('');
             $table->string('P', 50)->nullable()->default('');
             $table->string('status', 10)->nullable()->default('active');
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
        Schema::dropIfExists('export_billings');
    }
}
