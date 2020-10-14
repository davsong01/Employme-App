$table->integer('user_id');<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('program_id')->nullable;
            $table->integer('user_id')->nullable;
            $table->integer('t_amount')->nullable();
            $table->integer('balance')->nullable();
            $table->string('transid')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('t_location')->nullable();
            $table->string('paymenttype')->nullable();
            $table->integer('paymentStatus')->nullable();  
            $table->string('t_type')->nullable();
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
        Schema::dropIfExists('program_user');
    }
}
