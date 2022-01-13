<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->increments('id')->unsigned;
            $table->string('p_name');
            $table->string('p_abbr');
            $table->bigInteger('p_amount');
            $table->bigInteger('e_amount');
            $table->boolean('close_earlybird')->default(0);
            $table->date('p_start');
            $table->date('p_end');
            $table->string('image')->default('trainingimage/default.jpg');
            $table->string('booking_form')->nullable();
            $table->boolean('hascrm')->nullable()->default(0);
            $table->boolean('off_season')->nullable();
             
            $table->boolean('hasmock')->nullable()->default(0);
            $table->boolean('haspartpayment')->default(0);
            $table->boolean('status')->default(1);
            $table->boolean('verification')->nullable()->default(0);
            $table->boolean('hasresult')->nullable()->default(0);
            $table->boolean('close_registration')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('programs');
    }
}
