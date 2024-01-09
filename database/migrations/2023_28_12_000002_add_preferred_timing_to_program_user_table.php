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
        if(!Schema::hasColumn('programs', 'allow_flexible_payment')){
            Schema::table('programs', function (Blueprint $table) {
                $table->string('allow_flexible_payment')->nullable()->after('is_closed');
            });
        }

        if (!Schema::hasColumn('programs', 'allow_preferred_timing')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->string('allow_preferred_timing')->nullable()->after('is_closed');
            });
        }
   
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['allow_flexible_payment', 'allow_preferred_timing']);
        });
    }
};
