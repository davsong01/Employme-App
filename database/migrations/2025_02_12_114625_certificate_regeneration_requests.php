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
        Schema::create('certificate_regeneration_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('program_id');
            $table->integer('certificate_id')->nullable();
            $table->string('status')->default('pending');
            $table->json('meta')->nullable();
            $table->timestamp('preferred_date_of_issue');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_regeneration_requests');
    }
};
