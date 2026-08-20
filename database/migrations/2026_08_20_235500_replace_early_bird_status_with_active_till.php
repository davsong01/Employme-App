<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('programs') && ! Schema::hasColumn('programs', 'early_bird_active_till')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->timestamp('early_bird_active_till')->nullable()->after('e_amount');
            });
        }

        if (Schema::hasTable('programs') && Schema::hasColumn('programs', 'early_bird_status')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('early_bird_status');
            });
        }

        if (Schema::hasTable('groups') && Schema::hasColumn('groups', 'early_bird_status')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('early_bird_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('programs') && ! Schema::hasColumn('programs', 'early_bird_status')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->boolean('early_bird_status')->default(0)->after('e_amount');
            });
        }

        if (Schema::hasTable('groups') && ! Schema::hasColumn('groups', 'early_bird_status')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->boolean('early_bird_status')->default(0)->after('e_amount');
            });
        }

        if (Schema::hasTable('programs') && Schema::hasColumn('programs', 'early_bird_active_till')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('early_bird_active_till');
            });
        }
    }
};
