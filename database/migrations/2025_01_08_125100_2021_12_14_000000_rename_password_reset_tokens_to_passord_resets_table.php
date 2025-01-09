<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('password_resets')) {
            Schema::rename('password_resets', 'password_reset_tokens');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('password_reset_tokens')) {
            Schema::rename('password_reset_tokens', 'password_resets');
        }
    }
};