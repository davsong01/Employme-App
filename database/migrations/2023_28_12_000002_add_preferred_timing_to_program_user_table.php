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
        if (!Schema::hasColumn('program_user', 'preferred_timing')) {
            Schema::table('program_user', function (Blueprint $table) {
                $table->string('preferred_timing')->nullable()->after('program_id');
                $table->integer('admin_id')->nullable()->after('program_id');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_user', function (Blueprint $table) {
            $table->dropColumn(['admin_id']);
            $table->dropColumn(['preferred_timing']);
        });
    }
};
