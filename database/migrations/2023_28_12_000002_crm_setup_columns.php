<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if(!Schema::hasColumn('score_settings', 'crm_test')){
            Schema::table('score_settings', function (Blueprint $table) {
                $table->text('crm_test')->nullable()->after('role_play');
            });
        }

        if (!Schema::hasColumn('results', 'crm_test_score')) {
            Schema::table('results', function (Blueprint $table) {
                $table->text('crm_test_score')->nullable()->after('email_test_score');
            });
        }

        if (!Schema::hasColumn('result_threads', 'crm_test_score')) {
            Schema::table('result_threads', function (Blueprint $table) {
                $table->text('crm_test_score')->nullable()->after('email_test_score');
            });
        }

          
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('score_settings', function (Blueprint $table) {
            $table->dropColumn(['crm_test']);
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['crm_test_score']);
        });

        Schema::table('result_threads', function (Blueprint $table) {
            $table->dropColumn(['crm_test_score']);
        });
    }
};
