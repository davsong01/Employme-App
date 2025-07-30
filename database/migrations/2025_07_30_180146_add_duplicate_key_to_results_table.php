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
        Schema::table('results', function (Blueprint $table) {
            $table->string('duplicate_key')->nullable()->unique()->after('program_id');
        });

        // Backfill old records
        DB::statement("
            UPDATE results 
            SET duplicate_key = CONCAT(module_id, '-', user_id, '-', program_id)
        ");

        // Now enforce NOT NULL after backfill
        Schema::table('results', function (Blueprint $table) {
            $table->string('duplicate_key')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->deropColumn('duplicate_key');
        });

        Schema::table('result_threads', function (Blueprint $table) {
            $table->deropColumn('duplicate_key');
        });
    }
};
