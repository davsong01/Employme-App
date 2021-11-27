<?php

use Illuminate\Support\Facades\DB;
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

            $table->string('OFFICIAL_EMAIL')->nullable();
            $table->string('token')->nullable();
            
            $table->string('CURR_ABBREVIATION')->default('NGN');
            $table->text('ADDRESS_ON_RECEIPT')->default("IFECHUKWU HOUSE<br>Plot 87A Mustapha Azeeza Close, Off Alakoso Road<br /><small>(ABC Transport Terminal Axis) Lagos</small><br><br><br />");
            $table->string('DEFAULT_CURRENCY')->default("&#8358;"); 
            $table->string('program_coordinator')->nullable(); 
            $table->char('primary_color', 7)->default('#1F262D');
            $table->char('secondary_color', 7)->default('#b11a1a');
        });

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
