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
        Schema::table('coupon_users', function (Blueprint $table) {
            if (!Schema::hasColumn('coupon_users', 'group_id')) {
                $table->integer('group_id')->after('program_id')->nullable();
            }

            if (!Schema::hasColumn('coupon_users', 'transactionId')) {
                $table->string('transactionId')->after('group_id')->nullable();
            }

            if (!Schema::hasColumn('coupon_users', 'duplicate_check')) {
                $table->string('duplicate_check')->virtualAs(
                    "concat_ws('_', transactionId, email, status, coupon_id, program_id, group_id)"
                );
                $table->unique('duplicate_check', 'coupon_users_duplicate_check_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_users', function (Blueprint $table) {
            $table->dropColumn('group_id');
            $table->dropColumn('transactionId');
        });
    }
};
