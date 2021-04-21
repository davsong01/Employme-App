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
            $table->string('CURR_ABBREVIATION')->default('NGN');
            $table->text('ADDRESS_ON_RECEIPT')->nullable();
            $table->string('DEFAULT_CURRENCY')->default("&#8358;");
            $table->char('primary_color', 7)->default('#1F262D');
            $table->char('secondary_color', 7)->default('#b11a1a');
        });

        // DB::table('settings')->insert(
        //     array(
        //         'OFFICIAL_EMAIL' => 'name@domain.com',

        //     )
        // );

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
