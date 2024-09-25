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
        if (!Schema::hasColumn('programs', 'only_certified_should_see_certificate')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->enum('only_certified_should_see_certificate', ['yes', 'no'])
                ->after('allow_payment_restrictions_for_certificates')
                ->default('yes');
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn("metadata");
        });
    }
};