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
        if (!Schema::hasColumn('temp_transactions', 'preferred_timing')) {
            Schema::table('temp_transactions', function (Blueprint $table) {
                $table->string('preferred_timing')->nullable()->after('program_id');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_transactions', function (Blueprint $table) {
            $table->dropColumn(['preferred_timing']);
        });
    }
};
