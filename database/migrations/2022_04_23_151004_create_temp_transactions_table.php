<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type'); // part, earlybird, full
            $table->integer('program_id');
            $table->integer('coupon_id')->nullable();
            $table->integer('facilitator_id')->nullable();
            $table->float('amount');
            $table->string('email')->unique();
            
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
        Schema::dropIfExists('temp_transactions');
    }
}
