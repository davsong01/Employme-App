<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complains', function (Blueprint $table) {
            if (!Schema::hasColumn('complains', 'subject')) {
                $table->string('subject')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('complains', 'category')) {
                $table->string('category')->nullable()->after('subject');
            }

            if (!Schema::hasColumn('complains', 'follow_up_at')) {
                $table->date('follow_up_at')->nullable()->after('response');
            }

            if (!Schema::hasColumn('complains', 'tags')) {
                $table->string('tags')->nullable()->after('follow_up_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('complains', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('complains', 'subject') ? 'subject' : null,
                Schema::hasColumn('complains', 'category') ? 'category' : null,
                Schema::hasColumn('complains', 'follow_up_at') ? 'follow_up_at' : null,
                Schema::hasColumn('complains', 'tags') ? 'tags' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
