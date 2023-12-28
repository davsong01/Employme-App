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
        if(!Schema::hasColumn('programs', 'auto_certificate_settings')){
            Schema::table('programs', function (Blueprint $table) {
                $table->text('auto_certificate_settings')->nullable()->after('tech_percent');
            });
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['auto_certificate_settings']);
        });
    }
};
