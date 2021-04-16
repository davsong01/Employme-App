<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            //Payment gateway settings
            $table->text('PAYSTACK_PUBLIC_KEY');
            $table->text('PAYSTACK_SECRET_KEY');
            $table->text('PAYSTACK_PAYMENT_URL');
            $table->text('MERCHANT_EMAIL');

             //Mail settings
            $table->string('MAIL_DRIVER')->default('smtp');
            $table->string('MAIL_HOST')->default('smtp.googlemail.com');
            $table->string('MAIL_PORT')->default('587');
            $table->string('MAIL_USERNAME')->default('davedeloper@gmail.com');
            $table->string('MAIL_PASSWORD')->default('developerpassworD');
            $table->string('MAIL_ENCRYPTION')->default('tls');
           


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
