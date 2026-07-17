<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('programs', 'resolve_to_ids')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('resolve_to_ids');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('programs', 'resolve_to_ids')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->json('resolve_to_ids')->nullable()->after('program_lock');
            });
        }
    }
};
