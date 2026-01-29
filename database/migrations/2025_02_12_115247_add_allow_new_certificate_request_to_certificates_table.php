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
        if (!Schema::hasColumn('certificates', 'allow_new_certificate_request')) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->tinyInteger('allow_new_certificate_request')
                ->after('user_id')
                ->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('certificates')) {
            Schema::dropIfExists('allow_new_certificate_request');
        }
    }
};