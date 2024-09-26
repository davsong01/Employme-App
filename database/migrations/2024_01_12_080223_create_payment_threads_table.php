<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('payment_threads')) {
            Schema::create('payment_threads', function (Blueprint $table) {
                $table->id();
                $table->integer('program_id');
                $table->integer('user_id');
                $table->string('payment_id');
                $table->string('admin_id')->nullable();
                $table->string('t_type');
                $table->string('transaction_id');
                $table->string('parent_transaction_id');
                $table->decimal('amount', 11, 2);
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
        Schema::dropIfExists('payment_threads');
    }
}
