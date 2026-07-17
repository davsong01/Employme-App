<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('programs', 'certificate_template_id')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->unsignedBigInteger('certificate_template_id')
                    ->nullable()
                    ->index()
                    ->after('auto_certificate_settings');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('programs', 'certificate_template_id')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('certificate_template_id');
            });
        }
    }
};
