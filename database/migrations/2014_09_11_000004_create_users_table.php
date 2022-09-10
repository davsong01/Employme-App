<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('program_id')->unsigned();
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->integer('result_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role_id')->nullable();
            $table->string('t_phone')->nullable();
            $table->string('profile_picture')->nullable();
            $table->integer('paymentStatus')->nullable();
            $table->string('gender')->nullable();
            $table->string('t_type')->nullable();
            $table->integer('t_amount')->nullable();
            $table->integer('balance')->nullable();
            $table->string('transid')->nullable();
            $table->string('bank')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('t_location')->nullable();
            $table->string('paymenttype')->nullable();
            $table->boolean('hasResult')->default(0);

            $table->integer('responseStatus')->nullable();
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
