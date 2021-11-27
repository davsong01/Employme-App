<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_settings', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('program_id')->unsigned();
            // $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');

            $table->integer('certification')->unsigned();
            $table->integer('class_test')->unsigned();
            $table->integer('role_play')->unsigned();
            $table->integer('email')->unsigned();
            $table->integer('passmark')->unsigned();
            $table->integer('total')->unsigned();

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
        Schema::dropIfExists('score_settings');
    }
}
