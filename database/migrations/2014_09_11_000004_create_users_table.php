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
            $table->integer('result_id')->unsigned()->nullable();
            $table->integer('program_id')->unsigned()->nullable();//For Facilitators and Graders
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role_id')->nullable();
            $table->string('t_phone')->nullable();
            $table->string('profile_picture')->nullable();
            $table->text('profile')->nullable();
            $table->integer('facilitator_id')->nullable();
            $table->integer('off_season_availability')->nullable();
            $table->string('gender')->nullable();
            $table->string('earnings')->nullable();
            $table->string('earning_per_head')->nullable();
            $table->integer('responseStatus')->nullable();//For CRM
            
            $table->rememberToken();
            $table->timestamps();
            //To be deleted in production
            $table->integer('t_amount')->nullable();
            $table->integer('balance')->nullable();
            $table->string('transid')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('t_location')->nullable();
            $table->string('paymenttype')->nullable();
            $table->integer('paymentStatus')->nullable();  
            $table->string('t_type')->nullable();

            $table->integer('redotest')->default(0);
           

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
