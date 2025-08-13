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
            $table->integer('user_id')->after('program_id')->nullable();
            $table->tinyInteger('is_package')->after('program_id')->default(0);
            $table->json('program_ids')->after('is_package')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_transactions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('is_package');
            $table->dropColumn('program_ids');
        });
    }
};
