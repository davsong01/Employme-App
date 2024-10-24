<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->integer('amount');
                $table->string('type');
                $table->string('method');
                $table->string('provider')->nullable();
                $table->string('status')->default('pending');
                $table->string('transaction_id');
                $table->integer('admin_id')->nullable();
                $table->string('proof_of_payment')->nullable();
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
