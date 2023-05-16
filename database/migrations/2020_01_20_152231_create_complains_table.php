<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComplainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();

            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('other')->nullable();
            $table->string('state')->nullable();
            $table->string('lga')->nullable();
            $table->string('address')->nullable();
            $table->string('issues')->nullable();
            $table->integer('sla');
            $table->string('mode');
            $table->text('notes');
            $table->string('type');
            $table->text('content');
            $table->string('response')->nullable();
            $table->string('teamlead');
            $table->string('priority');
            $table->string('status');
            
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
        Schema::dropIfExists('complains');
    }
}
