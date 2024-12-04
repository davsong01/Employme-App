<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// php artisan migrate --path=/database/migrations/2024_12_04_121628_add_computation_status_to_modules_table.php

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('modules', 'computation_status')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->tinyInteger('computation_status')
                ->after('status')
                ->default(1);
            });
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn("computation_status");
        });
    }
};