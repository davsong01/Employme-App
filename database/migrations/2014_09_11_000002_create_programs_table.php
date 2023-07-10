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
            $table->integer('parent');
            $table->boolean('close_earlybird')->default(0);
            $table->date('p_start');
            $table->date('p_end');
            $table->string('image')->default('trainingimage/default.jpg');
            $table->string('booking_form')->nullable();
            $table->boolean('hascrm')->nullable()->default(0);
            $table->boolean('off_season')->nullable();
            $table->string('allow_payment_restrictions_for_materials')->default('yes');
            $table->string('allow_payment_restrictions_for_pre_class_tests')->default('yes');
            $table->string('allow_payment_restrictions_for_post_class_tests')->default('yes');
            $table->string('allow_payment_restrictions_for_results')->default('yes');
            $table->string('allow_payment_restrictions_for_certificates')->default('yes');
            $table->string('allow_payment_restrictions_for_completed_tests')->default('yes');
            $table->string('show_locations')->nullable();
            $table->string('show_modes')->nullable();
            $table->text('modes')->nullable();
            $table->text('locations')->nullable();
            $table->string('show_catalogue_popup')->nullable();

            $table->boolean('hasmock')->nullable()->default(0);
            $table->boolean('haspartpayment')->default(0);
            
            $table->integer('facilitator_percent')->default(0);
            $table->integer('admin_percent')->default(0);
            $table->integer('faculty_percent')->default(0);
            $table->integer('other_percent')->default(0);
            $table->integer('tech_percent')->default(0);

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
