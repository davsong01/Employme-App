<?php

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
        if (Schema::hasColumn('users', 'role_id')) {
            DB::statement("ALTER TABLE users CHANGE role_id roles VARCHAR(255) NULL");
        }


        if (Schema::hasColumn('users', 't_phone')) {
            DB::statement("ALTER TABLE users CHANGE t_phone phone VARCHAR(255) NULL");
        }

        if (Schema::hasColumn('program_user', 't_amount')) {
            DB::statement("ALTER TABLE program_user CHANGE t_amount amount VARCHAR(255) NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users CHANGE roles role_id VARCHAR(255) NULL");
    }
};
