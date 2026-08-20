<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('temp_transactions') && ! Schema::hasColumn('temp_transactions', 'payment_type')) {
            Schema::table('temp_transactions', function (Blueprint $table) {
                $table->string('payment_type', 20)->nullable()->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('temp_transactions') && Schema::hasColumn('temp_transactions', 'payment_type')) {
            Schema::table('temp_transactions', function (Blueprint $table) {
                $table->dropColumn('payment_type');
            });
        }
    }
};
