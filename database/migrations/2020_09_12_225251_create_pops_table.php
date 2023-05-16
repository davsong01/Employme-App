<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePopsTable extends Migration
{
    public function up()
    {
        Schema::create('pops', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('program_id');
            $table->string('email')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('bank');
            $table->double('amount');
            $table->string('location')->nullable();
            $table->date('date');
            $table->string('file');
          
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pops');
    }
}
