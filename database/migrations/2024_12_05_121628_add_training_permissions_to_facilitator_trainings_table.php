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
        if (!Schema::hasColumn('facilitator_trainings', 'training_permissions')) {
            Schema::table('facilitator_trainings', function (Blueprint $table) {
                $table->json('training_permissions')
                ->after('user_id')->nullable();
            });
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilitator_trainings', function (Blueprint $table) {
            $table->dropColumn("training_permissions");
        });
    }
};