<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->tinyInteger('show_certificate')->after('hasresult')->default(0);
        });

        DB::statement("
            UPDATE programs
            SET show_certificate = 1
            WHERE id IN (
                SELECT DISTINCT program_id FROM certificates WHERE program_id IS NOT NULL
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('show_certificate');
        });
    }
};
