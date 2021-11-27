<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMocksTable extends Migration
{
    public function up()
    {
        Schema::create('mocks', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('program_id')->unsigned();
            $table->integer('module_id')->unsigned();
            
            $table->integer('user_id')->unsigned();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->text('marked_by')->nullable();
            $table->text('grader')->nullable();
            $table->integer('class_test_score')->nullable();
            $table->text('class_test_details')->nullable();

            $table->integer('certification_test_score')->nullable();
            $table->text('certification_test_details')->nullable();
            
            $table->integer('role_play_score')->nullable();
            $table->integer('email_test_score')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mocks');
    }
}
