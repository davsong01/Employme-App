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
            $table->integer('group_id')->after('program_id')->nullable();
            $table->string('transactionId')->after('group_id')->nullable();

            $table->string('duplicate_check')->virtualAs(
                "concat_ws('_', transactionId, email, status, coupon_id,program_id,group_id)"
            );
            $table->unique('duplicate_check');
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
