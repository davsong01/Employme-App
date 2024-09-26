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
        if (!Schema::hasColumn('modules', 'allow_test_retake')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->tinyInteger('allow_test_retake')
                ->after('program_id')
                ->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn("allow_test_retake");
        });
    }
};