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
        if (!Schema::hasColumn('programs', 'allow_tests_retake')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->tinyInteger('allow_tests_retake')
                ->after('allow_payment_restrictions_for_certificates')
                ->default(0);
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn("allow_tests_retake");
        });
    }
};