<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_modes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('public_key');
            $table->string('secret_key');
            $table->string('merchant_email');
            $table->string('currency');
            $table->float('exchange_rate'); // amount will be multiplied by this everywhere there is amount in the system
            $table->string('currency_symbol');
            
            $table->string('others')->nullable();

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
        Schema::dropIfExists('payment_modes');
    }
}
