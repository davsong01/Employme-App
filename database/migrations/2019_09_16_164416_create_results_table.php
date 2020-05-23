<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('program_id')->unsigned();
            
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('class_test_score')->nullable();
            $table->integer('class_test_details')->nullable();
            $table->integer('certification_score')->nullable();
            $table->text('certification_details')->nullable();
            
            $table->integer('role_play_score')->nullable();
            $table->integer('email_test_score')->nullable();

            // $table->integer('program_id')->unsigned();
            // $table->unsignedInteger('workbookscore');
            // $table->unsignedInteger('emailscore');
            // $table->unsignedInteger('roleplayscore');
            // $table->unsignedInteger('certificationscore');
            // $table->unsignedInteger('passmark');
            // $table->unsignedInteger('total');
            // $table->string('status');
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
        Schema::dropIfExists('results');
    }
}
