<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// php artisan migrate --path=/database/migrations/2024_12_05_121628_add_training_permissions_to_facilitator_trainings_table.php

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('program_user', 'training_result')) {
            Schema::table('program_user', function (Blueprint $table) {
                $table->json('training_result')
                ->after('balance')->nullable();
            });
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_user', function (Blueprint $table) {
            $table->dropColumn("training_result");
        });
    }
};