<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $routes = [
            'certificates.manage.templates.index',
        ];

        DB::table('admins')->chunkById(100, function ($admins) use ($routes): void {
            foreach ($admins as $admin) {
                $roles = json_decode($admin->roles ?? '[]', true);
                if (!is_array($roles)) {
                    $roles = array_filter(array_map('trim', explode(',', (string) $admin->roles)));
                }

                if (!in_array('Admin', $roles, true)) {
                    continue;
                }

                $existing = json_decode($admin->menu_permissions ?? '[]', true);
                if (!is_array($existing)) {
                    $existing = [];
                }

                $merged = array_values(array_unique(array_merge($existing, $routes)));

                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update([
                        'menu_permissions' => json_encode($merged),
                        'updated_at' => now(),
                    ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $routes = [
            'certificates.manage.templates.index',
        ];

        DB::table('admins')->chunkById(100, function ($admins) use ($routes): void {
            foreach ($admins as $admin) {
                $roles = json_decode($admin->roles ?? '[]', true);
                if (!is_array($roles)) {
                    $roles = array_filter(array_map('trim', explode(',', (string) $admin->roles)));
                }

                if (!in_array('Admin', $roles, true)) {
                    continue;
                }

                $existing = json_decode($admin->menu_permissions ?? '[]', true);
                if (!is_array($existing)) {
                    $existing = [];
                }

                $filtered = array_values(array_diff($existing, $routes));

                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update([
                        'menu_permissions' => json_encode($filtered),
                        'updated_at' => now(),
                    ]);
            }
        });
    }
};
