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
            $table->date('p_start');
            $table->date('p_end');
            $table->string('booking_form')->nullable();
            $table->string('f_paid')->nullable();
            $table->string('p_paid')->nullable();
            $table->boolean('hascrm')->nullable()->default(0);
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
