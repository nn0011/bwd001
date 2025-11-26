<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExp4sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exp4s', function (Blueprint $table) {
            $table->increments('id');
			$table->string('A',50)->nullable();
			$table->string('B',50)->nullable();
			$table->string('C',50)->nullable();
			$table->string('D',50)->nullable();
			$table->string('E',50)->nullable();
			$table->string('F',50)->nullable();
			$table->string('G',50)->nullable();
			$table->string('H',50)->nullable();
			$table->string('I',50)->nullable();
			$table->string('J',50)->nullable();
			$table->string('K',50)->nullable();
			$table->string('L',50)->nullable();
			$table->string('M',50)->nullable();
			$table->string('N',50)->nullable();
			$table->string('O',50)->nullable();
			$table->string('P',50)->nullable();
			$table->string('Q',50)->nullable();
			$table->string('R',50)->nullable();
			$table->string('S',50)->nullable();
			$table->string('T',50)->nullable();
			$table->string('U',50)->nullable();
			$table->string('V',50)->nullable();
			$table->string('W',50)->nullable();
			$table->string('X',50)->nullable();
			$table->string('Y',50)->nullable();
			$table->string('Z',50)->nullable();
			$table->string('AA',50)->nullable();
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
        Schema::dropIfExists('exp4s');
    }
}
