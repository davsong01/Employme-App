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
        Schema::create('ai_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('program_id')->nullable();
            $table->string('provider_id')->nullable();
            $table->text('prompt');
            $table->text('response')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->string('status')->default('success'); // success, failed
            $table->foreignId('material_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_requests');
    }
};
