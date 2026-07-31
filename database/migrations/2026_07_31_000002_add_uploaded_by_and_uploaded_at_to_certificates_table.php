<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table): void {
            if (! Schema::hasColumn('certificates', 'uploaded_by')) {
                $table->foreignId('uploaded_by')->nullable()->after('program_id');
            }

            if (! Schema::hasColumn('certificates', 'uploaded_at')) {
                $table->timestamp('uploaded_at')->nullable()->after('uploaded_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table): void {
            if (Schema::hasColumn('certificates', 'uploaded_by')) {
                $table->dropConstrainedForeignId('uploaded_by');
            }

            if (Schema::hasColumn('certificates', 'uploaded_at')) {
                $table->dropColumn('uploaded_at');
            }
        });
    }
};
