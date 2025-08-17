<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('temp_transactions', function (Blueprint $table) {
            $table->string('exchange_rate')->after('currency_symbol')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_transactions', function (Blueprint $table) {
            $table->dropColumn('exchange_rate');
        });
    }
};
