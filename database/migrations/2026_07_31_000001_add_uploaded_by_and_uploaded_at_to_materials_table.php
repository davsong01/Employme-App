<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('uploaded_by')->nullable()->after('program_id');
            $table->timestamp('uploaded_at')->nullable()->after('uploaded_by');
        });

        DB::table('materials')
            ->whereNull('uploaded_at')
            ->update([
                'uploaded_at' => DB::raw('created_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropColumn('uploaded_at');
        });
    }
};
