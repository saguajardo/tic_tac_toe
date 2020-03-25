<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBoardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('board', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('0')->default(0);
            $table->integer('1')->default(0);
            $table->integer('2')->default(0);
            $table->integer('3')->default(0);
            $table->integer('4')->default(0);
            $table->integer('5')->default(0);
            $table->integer('6')->default(0);
            $table->integer('7')->default(0);
            $table->integer('8')->default(0);
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
        Schema::dropIfExists('board');
    }
}