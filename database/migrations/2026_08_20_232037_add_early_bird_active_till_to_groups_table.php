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
        if (Schema::hasTable('groups') && !Schema::hasColumn('groups', 'early_bird_active_till')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->timestamp('early_bird_active_till')->nullable()->after('e_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('groups') && Schema::hasColumn('groups', 'early_bird_active_till')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('early_bird_active_till');
            });
        }
    }
};
