<?php

use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('company_users', 'last_login')) {
            Schema::table('company_users', function (Blueprint $table) {
                $table->timestamp('last_login')
                ->after('password')
                ->nullable();
            });
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
