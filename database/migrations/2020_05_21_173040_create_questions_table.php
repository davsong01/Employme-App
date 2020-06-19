<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('module_id')->unsigned();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
           
            $table->text('title');
            $table->string('optionA')->nullable();
            $table->string('optionB')->nullable();
            $table->string('optionC')->nullable();
            $table->string('optionD')->nullable();
            $table->string('correct')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
