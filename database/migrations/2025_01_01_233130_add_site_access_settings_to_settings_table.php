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
        if (!Schema::hasColumn('settings', 'site_access_settings')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->json('site_access_settings')
                ->after('banner')
                ->nullable();
            });

            $siteSettings = [
                'admin_access' => 'enabled',
                'web_access' => 'enabled',
                'mobile_access' => 'enabled',
                'company_access' => 'enabled',
            ];

            Settings::first()->update(['site_access_settings' => $siteSettings]);
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
